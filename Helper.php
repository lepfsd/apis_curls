<?php

	function get_format_time(DateTime $time)
    {  
		$t = clone $time;
      	$t->setTimezone(new DateTimeZone("UTC"));
      	return $t->format("Y-m-d\TH:i:s\Z");
	}

	function json_response($message = null, $code = 200)
	{
		// clear the old headers
		header_remove();
		// set the actual code
		http_response_code($code);
		// set the header to make sure cache is forced
		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
		// treat this as json
		header('Content-Type: application/json');
		$status = array(
			200 => '200 OK',
			400 => '400 Bad Request',
			409 => 'CLIENT ERROR',
			500 => '500 Internal Server Error'
			);
		// ok, validation error, or failure
		header('Status: '.$status[$code]);
		// return the encoded json
		return json_encode(array(
			'status' => $code < 300, // success or not?
			'message' => $message
		));
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