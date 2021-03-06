<?php
        error_reporting(E_ALL);

        $executionStartTime = microtime(true) / 1000;
        // borders
        $url='https://gazetteer.craigbutton.co.uk/PHP/GetBorders.PHP?countryCode='. $_REQUEST['countryCode'];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
    
        $result=curl_exec($ch);
    
        curl_close($ch);
    
        $Coords = json_decode($result,true);
        $output['data']['borders']["coords"] = $Coords["data"]["coords"];
        $output['data']['borders']["type"] = $Coords["data"]["type"];
        // Country info
        $url='http://api.geonames.org/countryInfoJSON?country=' . $_REQUEST['countryCode']. '&username=cra74ig';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

        $decode = json_decode($result,true);
        
        $output["data"]["geonames"] = $decode["geonames"];

        $capital = $decode["geonames"][0]["capital"];
        $currency = $decode["geonames"][0]["currencyCode"];
        $countryName = $decode["geonames"][0]["countryName"];
        //fixes issue where spaces in capital city names would retreive null value by using url encoding
        $capital = str_replace(" ","%20",$capital);
        // Weather
        $url='http://api.weatherapi.com/v1/forecast.json?key=5006cbe9d8be4f33a27115359200511&q='.$capital.'&days=3&hour=12';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

        $decode = json_decode($result,true);	
        $output["data"]["Weather"]["current"] = $decode["current"];
        $output["data"]["Weather"]["day2"] = $decode["forecast"]["forecastday"][0]["day"];
        $output["data"]["Weather"]["day3"] = $decode["forecast"]["forecastday"][1]["day"];
        $output["data"]["Weather"]["day4"] = $decode["forecast"]["forecastday"][2]["day"];
        
        
        // Picture
        $url='https://pixabay.com/api/?key=18982067-e440b26cdd9f91df62d92ee06&q='.$capital.'+city&image_type=photo&pretty=true';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

        $decode = json_decode($result,true);
        if(isset($decode["hits"][0])){
                $output["data"]["webPicture"][0] = $decode["hits"][0]["webformatURL"];
                
                if(isset($decode["hits"][1])){
                        $output["data"]["webPicture"][1] = $decode["hits"][1]["webformatURL"];
                        
                        if(isset($decode["hits"][2])){
                                $output["data"]["webPicture"][2] = $decode["hits"][2]["webformatURL"];
                        }
                }
                
        }else{
                $url='https://pixabay.com/api/?key=18982067-e440b26cdd9f91df62d92ee06&q='.$countryName.'+city&image_type=photo&pretty=true';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$url);
        
                $result=curl_exec($ch);
        
                curl_close($ch);
        
                $decode = json_decode($result,true);
                if(isset($decode["hits"][0])){
                        $output["data"]["webPicture"][0] = $decode["hits"][0]["webformatURL"];
                        $test = $decode["hits"][0]["webformatURL"];
                        if(isset($decode["hits"][1])){
                                $output["data"]["webPicture"][1] = $decode["hits"][1]["webformatURL"];
                                if(isset($decode["hits"][2])){
                                        $output["data"]["webPicture"][2] = $decode["hits"][2]["webformatURL"];
                                }
                        }
                }
        }
       

        // exchange Rates
        $url='https://api.exchangeratesapi.io/latest?base='.$currency;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

        $decode = json_decode($result,true);
        
        $output['data']["currency"]["currentCurrency"] = $currency;
        if(isset($decode["rates"]["USD"])){
                $output['data']["currency"]["USD"] = round($decode["rates"]["USD"],2);
        }else{
                $output['data']["currency"]["USD"] = "n/a";
        }
        
        if(isset($decode["rates"]["GBP"])){
                $output['data']["currency"]["GBP"] = round($decode["rates"]["GBP"],2);
        }else{
                $output['data']["currency"]["GBP"] = "n/a";
        }
	if(isset($decode["rates"]["EUR"])){
		$output['data']["currency"]["EUR"] = round($decode["rates"]["EUR"],2);
	}else{
		$output['data']["currency"]["EUR"] = "n/a";
        }
        $url='https://newsapi.org/v2/top-headlines?sources=bbc-news&apiKey=6a1a275e031f48079eb063fedc943edc';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

	$decode = json_decode($result,true);
        
        $output['data']['news'] = $decode["articles"][0];

        // cities
        $url = 'http://geodb-free-service.wirefreethought.com/v1/geo/cities?offset=0&limit=3&minPopulation=500000&types=city&languageCode=en&countryIds='.$_REQUEST['countryCode'];
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

        $decode = json_decode($result,true);
        if(isset($decode['data'])){
                $output['data']['cities'] = $decode['data'];
                
                foreach($decode['data'] as $city){
                        $cityId = $city['id'];
                        
                        $url='https://wft-geo-db.p.rapidapi.com/v1/geo/cities/'.$cityId.'?rapidapi-key=ffe6b0389bmsh9bc50a78eef5cd5p143616jsne718a2a362ec';
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_URL,$url);

                        $result=curl_exec($ch);

                        curl_close($ch);
                        $decode = json_decode($result,true);
                        
                        $output['data']['cityData'][$cityId] = $decode['data'];
                        
                        
                        
                }
        }else{
                $url = 'http://geodb-free-service.wirefreethought.com/v1/geo/cities?offset=0&limit=3&types=city&languageCode=en&countryIds='.$_REQUEST['countryCode'];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$url);

                $result=curl_exec($ch);

                curl_close($ch);

                $decode = json_decode($result,true);
                if(isset($decode['data'])){
                        $output['data']['cities'] = $decode['data'];
                        
                        foreach($decode['data'] as $city){
                                $cityId = $city['id'];
                                
                                $ch = curl_init();
                                $url='https://wft-geo-db.p.rapidapi.com/v1/geo/cities/'.$cityId.'?rapidapi-key=ffe6b0389bmsh9bc50a78eef5cd5p143616jsne718a2a362ec';
                                
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_URL,$url);
                                
                                $result=curl_exec($ch);

                                curl_close($curl);
                                $decode = json_decode($result,true);
                                
                                $output['data']['cityData'][$cityId] = $decode['data'];
                                
                        }
                }
        }
        
        // wikiLinks
        $url='http://api.geonames.org/wikipediaSearchJSON?q=' . $capital."&countryCode=". $_REQUEST['countryCode']. '&maxRows=3&username=cra74ig';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

	$decode = json_decode($result,true);
        
        $output['status']['code'] = "200";
        $output['status']['name'] = "ok";
        $output['status']['description'] = "data returned";
        $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
        $output['data']['WikiLinks'] = $decode["geonames"];



        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode($output); 
?>