<?php

function mysql_urlencode() {
	global $db;
	
	$db->Execute("DELIMITER ;

DROP FUNCTION IF EXISTS urlencode;

DELIMITER |

CREATE FUNCTION urlencode (s VARCHAR(4096)) RETURNS VARCHAR(4096)
DETERMINISTIC 
CONTAINS SQL 
BEGIN
       DECLARE c VARCHAR(4096) DEFAULT '';
       DECLARE pointer INT DEFAULT 1;
       DECLARE s2 VARCHAR(4096) DEFAULT '';

       IF ISNULL(s) THEN
           RETURN NULL;
       ELSE
       SET s2 = '';
       WHILE pointer <= length(s) DO
          SET c = MID(s,pointer,1);
          IF c = ' ' THEN
             SET c = '+';
          ELSEIF NOT (ASCII(c) BETWEEN 48 AND 57 OR 
                ASCII(c) BETWEEN 65 AND 90 OR 
                ASCII(c) BETWEEN 97 AND 122) THEN
             SET c = concat("%",LPAD(CONV(ASCII(c),10,16),2,0));
          END IF;
          SET s2 = CONCAT(s2,c);
          SET pointer = pointer + 1;
       END while;
       END IF;
       RETURN s2;
END;
 
|

DELIMITER ;");
}

function mysql_urldecode() {
	global $db;
	
	$db->Execute("DROP FUNCTION IF EXISTS urldecode");

	$db->Execute("CREATE FUNCTION urldecode (s VARCHAR(4096)) RETURNS VARCHAR(4096)
DETERMINISTIC 
CONTAINS SQL 
BEGIN
       DECLARE c VARCHAR(4096) DEFAULT '';
       DECLARE pointer INT DEFAULT 1;
       DECLARE h CHAR(2);
       DECLARE h1 CHAR(1);
       DECLARE h2 CHAR(1);
       DECLARE s2 VARCHAR(4096) DEFAULT '';

       IF ISNULL(s) THEN
          RETURN NULL;
       ELSE
       SET s2 = '';
       WHILE pointer <= LENGTH(s) DO
          SET c = MID(s,pointer,1);
          IF c = '+' THEN
             SET c = ' ';
          ELSEIF c = '%' AND pointer + 2 <= LENGTH(s) THEN
             SET h1 = LOWER(MID(s,pointer+1,1));
             SET h2 = LOWER(MID(s,pointer+2,1));
             IF (h1 BETWEEN '0' AND '9' OR h1 BETWEEN 'a' AND 'f')
                 AND
                 (h2 BETWEEN '0' AND '9' OR h2 BETWEEN 'a' AND 'f') 
                 THEN
                   SET h = CONCAT(h1,h2);
                   SET pointer = pointer + 2;
                   SET c = CHAR(CONV(h,16,10));
              END IF;
          END IF;
          SET s2 = CONCAT(s2,c);
          SET pointer = pointer + 1;
       END while;
       END IF;
       RETURN s2;
END;");

}

?>