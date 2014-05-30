#!/usr/bin/perl

# Things to know about perl:
#	- A hash (%variable) is some sort of associative array. Use it like %variable{$key}.
#	- A non-associative array's being used like @variable to define the array, to call that array use $variable.
#	You call a subroutine (function) with &function_name();

#	If statements
#	- To use an 'else if' statement, use elsif
#	- To compare a string against something else use eq (equal) or ne (not equal). Only to compare numbers you use == or !=.

# Include the libraries we use
use Time::Local;
# The MD5 function we use in PHP is called md5_hex in perl
use Digest::MD5 qw(md5 md5_hex);
# use Data::Dumper;
use PerlIO::gzip;

use Geo::IP;
my $gi = Geo::IP->new(GEOIP_STANDARD);

# $stime = time();

# This is so we can convert 3-character months to numbers
%month_num = (
	"Jan" => 0,
	"Feb" => 1,
	"Mar" => 2,
	"Apr" => 3,
	"May" => 4,
	"Jun" => 5,
	"Jul" => 6,
	"Aug" => 7,
	"Sep" => 8,
	"Oct" => 9,
	"Nov" => 10,
	"Dec" => 11
);

# Declare the subroutines/functions

# Store line is a found subroutine/function, that does some counting
sub store_line {
	# store one line's worth of visit data
	my($host, $date, $time, $url, $referer, $agent) = @_;
	my $seconds = &get_seconds($date, $time);

	if ($visit_num{$host}) {
		# there is a visit currently "working" for this host
		my $visit_num = $visit_num{$host};
		my $elapsed = $seconds - $last_seconds{$visit_num};
		if (($expire_time) and ($elapsed > $expire_time)) {
			# this visit has expired, so start a new one
			&new_visit($host, $date, $time, $url, $seconds, 
			$referer, $agent);
		} else {
			# this visit has not expired, so add to existing record
			&add_to_visit($host, $date, $time, $url, $seconds, $elapsed);
		}
	} else {
		# there is no visit currently "working" for this host
		&new_visit($host, $date, $time, $url, $seconds,
		$referer, $agent);
	}
}

# This counts the seconds in the current session
sub get_seconds {
	my ($date, $time) = @_;
	my ($day, $mon, $yr) = split /\//, $date;
	my ($hr, $min, $sec) = split /:/, $time;
	
	$mon = $month_num{$mon};
	$yr = $yr - 1990;
	
	my $seconds = timelocal($sec, $min, $hr, $day, $mon, $yr);
}

sub new_visit {
	# record an entry for an access line that has been
	# determined to represent a new visit (either because
	# this is the first time this host has been seen,
	#or because the host's previous visit has expired).
	
	my($host, $date, $time, $url, $seconds, $referer, $agent) = @_;
	
	my $visit_num = ++$total_visits;
	$visit_num{$host} = $visit_num;
	$sessioncounter++;
	$last_seconds{$visit_num} = $seconds;
}

sub add_to_visit {
	# append to an existing visit recond because it has been
	# determinded that the current line contains more data to
	# be added to a currently "working" visit
	
	my($host, $date, $time, $url, $seconds, $elapsed) = @_;
	my $visit_num = $visit_num{$host};
	$last_seconds{$visit_num} = $seconds;
	
	my $elapsed_string = (int ($elapsed / 60)) . ":" . sprintf "%20u", $elapsed % 60;
}

sub parse_log_line {
	# This is the regex that parses the current log line
	
	if($logformat eq 'ApacheCommonNoReferrerLogParser') { # No referer, no agent
		$parse_regex = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] "(\S+) (.+?) (\S+)" (\S+) (\S+)$/';
	} elsif ($logformat eq 'ApacheCombinedLogParser') { # No Cookie
		$parse_regex = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] "(\S+) (.+?) (\S+)" (\S+) (\S+) "([^"]+)" "([^"]+)"$/';
	} elsif ($logformat eq 'LooseApacheCommonLogParser') { # No Cookie
		$parse_regex = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] "(\S+) (.+?) (\S+)" (\S+) (\S+) "([^"]+)" "([^"]+)"$/';
	} elsif ($logformat eq 'ApacheCombinedCookieLogParser') {
		$parse_regex = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] "(\S+) (.+?) (\S+)" (\S+) (\S+) "([^"]+)" "([^"]+)" "([^"]+)"$/';
	}
	
	my ($host, $ident_user, $auth_user, $date, $time, $time_zone, $method, $url, $protocol, $status, $bytes, $referer, $agent, $cookie) = eval($parse_regex);
	
	$output = "";
	
	# If there is no host, the line couldn't be parsed, so we skip it
	if(!$host) { next; }
	
	# Set bytes to 0 bytes, if not defined/found in line
	if(!$bytes || $bytes eq '-') { $bytes = 0; }
	
	# Split Date and Time to variables
	my ($day, $mon, $yr) = split /\//, $date;
	my ($hr, $min, $sec) = split /:/, $time;
	
	# Skip Time
	if($force == 0) {
		next if timelocal($sec, $min, $hr, $day, $month_num{$mon}, $yr) <= $skiptime;
	}
	
	# Create a key for bandwidth hash (assoc array)
	# Because perl starts month counts at 0, we want to add 1 to the month key, so bandwidth has the right month
	$mon_key = ($month_num{$mon} + 1);
	if($mon_key == 13) {
		$mon_key = 1;
		$yr = $yr + 1;
	}
	if(length($mon_key) == 1) {
		$date_key = $yr."0".$mon_key.$day.$hr;
	} else {
		$date_key = $yr.$mon_key.$day.$hr;
	}
	
	# Count the bytes per day
	if(exists $total_bytes{$date_key}) {
		$total_bytes{$date_key} = $total_bytes{$date_key} + $bytes;
	} else {
		$total_bytes{$date_key} = 0;
	}
	
	# Simple bot detection; we'll do a more advanced detection in PHP (update_summaries)
	if($agent =~ /(bot|spider|crawl|slurp)$/i) {
		$crawl = 1;
	} else {
		$crawl = 0;
	}
	
	# Parse URL Parameters (url params, urlparams)
	if($url && $url ne "") {
		next if $url =~ /($skipfiles)/gi;
		
		if(index($url, '?') > -1) {
			$urlparams = substr($url, index($url, '?'));
			$url = substr($url, 0, index($url, '?'));
			
			# Here we strip the important parameters from the urlparams, and put it into url
			while (($key, $value) = each(%dynamic_pages)) {
				if($key eq $url && index($urlparams, $value.'=') > -1) {
					$urlparam_part = substr($urlparams, index($urlparams, $value.'='));
					if(index($urlparam_part, '&') > -1) {
						$urlparam_part = substr($urlparams, index($urlparams, '&'));
					}
					
					
					$urlparams =~ s/\?//gi;
					$urlparams =~ s/($urlparam_part)//gi;
					
					$url = $url.'?'.$urlparam_part;
				}
			}
			
			if($urlparamfilter ne '' && $urlparams ne '' && $urlparams =~ /($paramfilter_regex)/gi) {
				$urlparams = '';
			}
		} else {
			$urlparams = '';
		}
		# Let's do some include/exclude params here
	}
	
	# If the host matches skip ips: skip it
	if($host && $host ne "" && $skipips ne "") {
		next if $host =~ /($skipips)$/gi;
	}
	
	# Create visitorid, $vidm contains the visitorid method
	if($vidm eq 'VIDM_IPADDRESS') {
		$visitorid = md5_hex($host);
	} elsif ($vidm eq 'VIDM_IPPLUSAGENT') {
		$visitorid = md5_hex($host . ':' . $agent);
	} elsif ($vidm eq 'VIDM_COOKIE') {
		if(index($cookie, 'Logaholic_VID') > -1) {
			$cookiepart = substr($cookie, (index($cookie, "Logaholic_VID") + 14));
			$cookiepart = substr($cookiepart, 0, index($cookiepart, ";"));
			
			$visitorid = $cookiepart;
		} else {
			$visitorid = md5_hex($host . ':' . $agent);
		}
	}
	
	# Parse Referrer Parameters (refparams) and keywords
	if($referer && $referer ne "") {
		$keywords = "";
		
		$tmp_confdomain = '://'.$confdomain;
		$referer =~ s/($equivdomains)/$tmp_confdomain/gi;
		
		if(index($referer, '?') > -1) {
			$refparams = substr($referer, index($referer, '?'));
			$referer = substr($referer, 0, index($referer, '?'));
			
			if($urlparamfilter ne '' && $refparams ne '' && $refparams =~ /($paramfilter_regex)/gi) {
				$refparams = '';
			}
			
			# Here we strip the important parameters from the urlparams, and put it into url
			while (($key, $value) = each(%dynamic_pages)) {
				if($confdomain.$key eq $referrer && index($refparams, $value.'=') > -1) {
					$refparam_part = substr($refparams, index($refparams, $value.'='));
					if(index($refparam_part, '&') > -1) {
						$refparam_part = substr($refparams, index($refparams, '&'));
					}
					
					$refparams =~ s/\?//gi;
					$refparams =~ s/($urlparam_part)//gi;
					
					$referer = $referer.'?'.$refparam_part;
				}
			}
			
			# If the referrer is google, we look at our predefined 'keyworddetectors'
			if(index($referer, "google") > -1) {
				for($i = 0; $i < (scalar(@keyworddetectors) - 1); $i++) {
					if(index($refparams, $keyworddetectors[$i]) > -1) {
						$keywordpart = substr($refparams, (index($refparams, $keyworddetectors[$i]) + length($keyworddetectors[$i])));
						if(index($keywordpart, '&') > -1) {
							$keywords = substr($keywordpart, 0, index($keywordpart, "&"));
						} else {
							$keywords = $keywordpart;
						}
					}
				}
				
				# Google Params
				# For each parameter in googleparams, remove it from refparams
				if($refparams =~ /($googleparams)/gi) {
					@tmp = split('&', $refparams);
					@stripped_params = ();
					$c = 0;
					
					for($i = 0; $i < (scalar(@tmp) - 1); $i++) {
						@tmp_param = split('=', $tmp[$i]);
						if(index($googleparams, $tmp_param[0]) > -1) {
							$stripped_params[$c] = $tmp_param[0].'='.$tmp_param[1];
							$c++;
						}
					}
					
					$refparams = join("&", @stripped_params);
				}
			}
			
			# If the referrer is yahoo, we look for ?p= or &p=
			if(index($referer, "search.yahoo") > -1) {
				$keywordpart = substr($refparams, (index($refparams, "p=") + 2));
				if(index($keywordpart, '&') > -1) {
					$keywords = substr($keywordpart, 0, index($keywordpart, "&"));
				} else {
					$keywords = $keywordpart;
				}
			}
			
			# If it's not google, nor yahoo, we look for ?q= or &q=
			if(index($refparams, 'q=') > -1) {
				$keywordpart = substr($refparams, (index($refparams, 'q=') + 2));
				if(index($keywordpart, '&') > -1) {
					$keywords = substr($keywordpart, 0, index($keywordpart, "&"));
				} else {
					$keywords = $keywordpart;
				}
			}
			
			# If the referrer is google, but we haven't found a keyword, we'll set the keyword to 'Not Provided'
			if(index($referer, "google") > -1 && $keywords eq "") {
				$keywords = "(Not Provided)";						
			}
		} else {
			# There are no referrer parameters
			$refparams = '';
		}
	} else {
		$keywords = "";
	}
	
	# Count the total amount of hits
    ++$total_hits;
	
	# Count the total amount of megabytes
    $total_mb += ($bytes / (1024 * 1024));
	
	# Count the toal amount of views (Same as hits?)
    ++$total_views;
	
	# Do the 'store_line' function (a function is called subroutine in Perl)
	# In this function/subroutine we count stuff
    &store_line($host, $date, $time, $url, $referer, $agent);
	
	# Detect the country
	$country = $gi->country_code_by_addr($host);
	
	# This is one line of output, printed in the /mysqltmp/ log file
	# We seperate each field with |LWA| ([pipe]LWA[pipe])
	$output = join "|LWA|",
	# print join "|LWA|",
		$host,
		$ident_user,
		$auth_user,
		timelocal($sec, $min, $hr, $day, $month_num{$mon}, $yr),
		$time_zone,
		$url,
		$status,
		$bytes,
		$referer,
		$agent,
		md5_hex($agent),
		$cookie,
		$urlparams,
		$refparams,
		$keywords,
		$visitorid,
		$country,
		$crawl,
		$sessioncounter."\n";
}

# Open the config file
open FILE, $ARGV[0] or die $!;

# Loop through the config file, and create variables for each line
while (<FILE>) {
	my @tmp = split(/=/, $_);

	$tmp_key = $tmp[0];
	$tmp_val = $tmp[1];
	
	$tmp_val =~ s/\n//gi;
	
	$$tmp_key = $tmp_val;
}

close FILE;

# Configure the important parameters here
%dynamic_pages = ();
if($importantparams ne '') {
	@tmp_important = split(',', $importantparams);
	
	for($i = 0; $i < scalar(@tmp_important); $i++) {
		@tmp_param = split("::", $tmp_important[$i]);
		$dynamic_pages{$tmp_param[0]} = $tmp_param[1];
	}
}

# Remove linebreaks from $urlparamfiltermode
if($urlparamfiltermode ne '') {
	$urlparamfiltermode =~ s/\n//gi;
}

if($urlparamfilter ne '') {
	# Prepare the filter by removing linebreaks and spaces from the filter
	$urlparamfilter =~ s/\n//gi;
	$urlparamfilter =~ s/ //gi;
	
	# Create our filter regex by replaceing commas with pipes
	if($urlparamfiltermode eq 'Include') {
		$paramfilter_regex = "";
	} else {
		$paramfilter_regex = $urlparamfilter;
		$paramfilter_regex =~ s/,/|/gi;
		@tmp_regex = split('\|', $paramfilter_regex);
		$paramfilter_regex = '(\?|\&)'.join("=|(\?|\&)", @tmp_regex) . '=';
	}
	
	$urlparamfilter = '&' . $urlparamfilter . '=';
	$urlparamfilter =~ s/,/=,&/gi;
	
	# $urlparamfilter now contains a valid regex we can use
}

# The amount of time for one session
$expire_time = 1200;

# These are our defined search engine parameters, used for keyword detection
@keyworddetectors = ('as_q', 'as_epq', 'as_oq', 'as_eq', 'as_sitesearch', 'as_rq', 'as_lq');

$sessioncounter = 0;
$total_visits = '';

$urlparams = '';
$refparams = '';
$keywords = '';

$totaltime = time();
$amountoflines = 0;

if(substr($inputfile, -3) eq '.gz') {
	# If we have a gz file, we want to open it and get the first log line
	open INPUTFILE, "<:gzip", "$inputfile" or die $!;
	$firstlogline = <INPUTFILE>;
	close (INPUTFILE);
} else {
	# If we have a log file, we want to open it and get the first log line
	open (INPUTFILE, "$inputfile") || die ("Could not open file <br> $!");
	$firstlogline = <INPUTFILE>;
	# $amountoflines += tr/\n/\n/ while sysread(INPUTFILE, $_, 2 ** 16);
	close (INPUTFILE);
}
# print "Amount of lines: $amountoflines\n";

# $outputfile is being defined in our config file
open OUTPUTFILE, '>', $outputfile or die $!;

# Define some variables
my($total_hits, $total_mb, $total_views);
%total_bytes;

# $inputfile is being defined in the config file
if(substr($inputfile, -3) eq '.gz') {
	# Open the gz file, and start parsing the lines.
	open INPUTFILE, "<:gzip", "$inputfile" or die $!;
	
	if($force == 0) {
		# Set the file pointer to the last known file pointer position (used for incremental updates)
		seek(INPUTFILE, $lastpos, 0);
	}
	
	while (<INPUTFILE>) {
		# The variable $_ is a magic variable containing the current line
		$output = &parse_log_line($_); # This function/subroutine parses the current log line and return our own format
		
		print OUTPUTFILE $output; # We print the preparsed line in our outputfile
	}
} else {
	# Open the log file, and start parsing the lines.
	open INPUTFILE, " < " . "$inputfile" or die "can't open input file $!";

	if($force == 0) {
		# Set the file pointer to the last known file pointer position (used for incremental updates)
		seek(INPUTFILE, $lastpos, 0);
	}
	
	while (<INPUTFILE>) {
		# The variable $_ is a magic variable containing the current line
		$output = &parse_log_line($_); # This function/subroutine parses the current log line and return our own format
		
		print OUTPUTFILE $output; # We print the preparsed line in our outputfile
	}
}

# We'll fetch the last log position
if(substr($inputfile, -3) eq '.gz') {
	$lastlogpos = tell(INPUTFILE);
} else {
	$lastlogpos = tell(INPUTFILE);
}

# We're done writing our preparsed log, so we close it
close OUTPUTFILE;

# We're done writing our preparsed log, so we close the log file
close INPUTFILE;

# Set the outputfile permissions to world-usable
$mode = 0777;
chmod($mode, $outputfile)."\n";

$firstlogline =~ s/\n//gi;

# We print some variables here, which will be catched in PHP ($output variable in /includes/do_perl.php)
print "lastlogpos:".$lastlogpos."\n";
print "firstlogline:".$firstlogline."\n";
while (($key, $value) = each(%total_bytes)) {
	print "bandwidth:".$key."=".$value."\n";
}

# print (time() - $stime)."\n";