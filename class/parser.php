<?php


class Parser {
	function inbox($data)
	{

		$result = array();

		$imap = imap_open($data['email']['hostname'], $data['email']['username'], $data['email']['password']) or die ('Conexion fallida: ' . imap_last_error());

		if ($imap) {

			$result['status'] = 'success';
			$result['email']  = $data['email']['username'];


			$date = date('j F Y',strtotime('24 March 2017'));
			//$read = imap_search($imap,'SINCE "'.$date.'"' );
			$read = imap_search($imap,'UNFLAGGED');


			//$read = imap_search($imap, 'ALL');

			if($data['pagination']['sort'] == 'DESC'){
				rsort($read);
			}
			//print_r(imap_body($imap, $read[0], 0));
			//echo "<br><br> Msjs Total";
			$num = count($read);
			//echo "<br><br>";
			$result['count'] = $num;

			$stop = $data['pagination']['limit'] + $data['pagination']['offset'];

			if($stop > $num){
				$stop = $num;
			}

			for ($i = $data['pagination']['offset']; $i < $num; $i++) {

				$overview   = imap_fetch_overview($imap, $read[$i], 0); //DETALLES CABECERA DEL EMAIL
				$message    = imap_body($imap, $read[$i], 0); //BODY1 DEL EMAIL INCLUYE HTML TAGS
				$header     = imap_headerinfo($imap, $read[$i], 0); //DETALLES CABECERA DEL EMAIL

				//echo $i;
				//echo "<pre>";
				//print_r($overview);
				//echo "</pre>";

				$date = $overview[0]->date;
				$email_from = $header->from[0]->mailbox . '@' . $header->from[0]->host; //EMAIL FROM
				if (preg_match('/Content-Type/', $message)) {
					$content = explode('Content-Type: ', $message);
					$message = $content;
				} else {
					$message = $message;
				}
				if (strpos($message[1],'N=C3=BAmero de Cuenta')!==false) {
					$pos_1=strpos($message[1],'N=C3=BAmero de Cuenta');
					$pos_2=strpos($message[1],'--');
					$fields = substr(strip_tags($message[1]), $pos_1);
					$fields_sep = explode("\r\n", $fields);

					//**********************************************
					//**********************************************
          			$flags=[];
					foreach ($fields_sep as $key=>$value) {
						$field = explode(':',$value);

						if ( (count($field)==2) && (strpos($field[0],'N=C3=BAmero de Cuenta')!==false) ) {
							$campos['numero de cuenta']=$field[1];
							$last = 'numero de cuenta';
						}elseif ( (count($field)==2) && (strpos($field[0],'Kit a instalar')!==false) ) {
							$campos['kit a instalar']=$field[1];
							$last = 'kit a instalar';
						}elseif ( (count($field)==2) && (strpos($field[0],'Nombre del cliente')!==false) ) {
							$campos['nombre del cliente']=$field[1];
							$last = 'nombre del cliente';
						}elseif ( (count($field)==2) && (strpos($field[0],'Direcci=C3=B3n')!==false) ) {
							$campos['direccion']=$field[1];
							$last = 'direccion';
						}elseif ( (count($field)==2) && (strpos($field[0],'Referencias Domicilio')!==false) ) {
							$campos['referencias domicilio']=$field[1];
							$last = 'referencias domicilio';
						}elseif ( (count($field)==2) && (strpos($field[0],'Tel=C3=A9fono')!==false) ) {
							$campos['telefono']=$field[1];
							$last = 'telefono';
						}elseif ( (count($field)==2) && (strpos($field[0],'e-mail')!==false) ) {
							$campos['e-mail']=$field[1];
							$last = 'e-mail';
						}elseif ( (count($field)==2) && (strpos($field[0],'Observaciones')!==false) ) {
							$campos['observaciones']=$field[1];
							$last = 'observaciones';
						}elseif ( (count($field)==1) && (!$field[0]=='') ){
							$campos[$last] .= $field[0];
						}else{
              				$flags[]=$overview['0']->uid;
							break;
						}
					}
			        $campos['observaciones'] = str_replace(",",";",$campos['observaciones']);

					//**********************************************
					//**********************************************
			        $status = imap_setflag_full($imap, $overview['0']->uid, "\\Flagged",SE_UID);
			        //echo "<pre>";
					//print_r($status);
					//echo "</pre>";

			        $result['mails'][$overview['0']->uid] = array(
			            // 'status' => $status,
			            // 'id' => strtotime($date),
			            // 'date' => $date,
			            // 'email_from' => $email_from,
			            'fields' => $campos
			            //'flags' => $flags
			        );

				}
			}

			imap_close($imap);

		} else {
			$result['status'] = 'error';
		}
    if (isset($result['mails'])){
      return $result['mails'];
    } else {
      return false;

    }

	}
}

 ?>
