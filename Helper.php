<?php

	function get_format_time(DateTime $time)
    {  
		$t = clone $time;
      	$t->setTimezone(new DateTimeZone("UTC"));
      	return $t->format("Y-m-d\TH:i:s\Z");
	}

	function validate_type($data, $typeData) {

        $error = '';
        $auxiliar = 0;

        if (empty($data) || empty($typeData)) {
            $error = "Sent parameters are not correct";
        }
        
        foreach ($data as $valor) {
            if ($typeData[$auxiliar]=="double"){
                if (gettype($valor) !== $typeData[$auxiliar] && gettype($valor) !=="integer"){
                    $error = "Parameter with different type of data";
                }
            }else{
                if (gettype($valor) !== $typeData[$auxiliar]) {
                    $error = "Parameter with different type of data";
                }
            }   
            if (gettype($valor) === "NULL") {
                $error = "Sent parameters are not correct";
            }

            $auxiliar++;
        }

        if ($error !== '') {
            return $error;
		}
		
		return "OK";
		
    }