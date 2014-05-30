

	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/plusslider.css" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
	<script type='text/javascript' src='js/jquery.plusslider.js'></script>
	<script type='text/javascript' src='js/jquery.easing.1.3.js'></script>
	<script type='text/javascript'>
	$(document).ready(function(){

		$('#slider').plusSlider({
			sliderEasing: 'easeInOutExpo', // Anything other than 'linear' and 'swing' requires the easing plugin
			autoPlay: true,
			paginationPosition: 'append',
			sliderType: 'slider' // Choose whether the carousel is a 'slider' or a 'fader'
		});

		$('#slider2').plusSlider({
			displayTime: 2000, // The amount of time the slide waits before automatically moving on to the next one. This requires 'autoPlay: true'
			sliderType: 'fader', // Choose whether the carousel is a 'slider' or a 'fader'
			width: 500, // Overide the default CSS width
			height: 250 // Overide the default CSS width
		});

		$('#slider3').plusSlider({
			sliderEasing: 'easeInOutExpo', // Anything other than 'linear' and 'swing' requires the easing plugin
			fullWidth: true,
			sliderType: 'slider' // Choose whether the carousel is a 'slider' or a 'fader'
		});

	});
	</script>

	<div id="page-wrap">

		<div id="content">
  
			<div id="slider3">
				<div data-title="Quote" class="quote">
					I do not fear death,<br />
					in view of the fact that I had been dead<br />
					for billions and billions of years<br />
					before I was born, and had not suffered<br />
					the slightest inconvenience from it.<br />
					- Mark Twain
				</div>
				<div data-title="Quote2" class="quote2">
					The difference between the right word<br />
					and the almost right word is the difference<br />
					between lightning and a lightning bug.<br />
					- Mark Twain
				</div>
				<div data-title="Quote3" class="quote3">
					Nature knows no indecencies;<br />
					man invents them.<br />
					- Mark Twain
				</div>
			</div>

			
		</div>
	</div>
