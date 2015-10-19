<?php
// désactivation msg d'erreur
ini_set('display_errors','off');

// on se connecte à MySQL
$db = mysql_connect('mysql.hostinger.fr', 'u596457159_user', 'Framar01');

// on sélectionne la base
mysql_select_db('u596457159_front',$db);

// on crée la requête SQL
$sql = 'SELECT LP.FIRSTNAME AS prenom, LP.LASTNAME AS nom, LP.EMAIL AS email,LP.participant_id as participant_id,LT.tid as token_id
FROM lime_survey_links LSL
JOIN lime_participants LP ON LP.PARTICIPANT_ID = LSL.PARTICIPANT_ID
JOIN lime_surveys LS ON LS.SID = LSL.SURVEY_ID
JOIN lime_tokens_532851 LT ON LT.PARTICIPANT_ID = LP.PARTICIPANT_ID
WHERE LSL.date_completed is not null AND   DATE_FORMAT(LT.COMPLETED, "%Y-%m-%d" ) = CURDATE( )
AND NOT
EXISTS (
SELECT 1
FROM eg_suivi_envoi egs
WHERE egs.token_id = LT.tid
AND egs.participant_id = LT.participant_id
)
';
//AND LT.COMPLETED !='N'

// on envoie la requête
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

    echo 'Données interrogées.'; 
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {
    // on affiche les informations de l'enregistrement en cours
 //   echo '<b>'.$data['prenom'].$data['nom'].$data['email'].'</b> ';
     // Plusieurs destinataires à optimiser car la fonciton mail n'est pas top pour cela
     $to  = $data['email']; // notez la virgule
  //   $to .= 'fmarguin@garsys.fr';
     $subject = 'Le sujet Mr mail'.$data['nom'];
     $message = 'Bonjour !'.$data['participant_id'];
     $headers = 'From: no-reply@entre-generations.com' . "\r\n" .
     'Reply-To: no-reply@entre-generations.com' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();

    // echo 'Le sujet Mr mail'.$data['nom'];
    // on écrit la requête sql
	//, participant_id, date_envoi, token_id, date_envoi_mail,  date_maj_table
	   $sql = "INSERT INTO eg_suivi_envoi(abonne_id, participant_id, date_envoi, token_id, date_envoi_mail,  date_maj_table) 
	   VALUES ('".$data['participant_id']."','".$data['participant_id']."',now(),'".$data['token_id']."',now(),now())";
    //,$data['participant_id'],now(),$data['token_id'],now(),now()
    // on insère les informations du formulaire dans la table
    mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error());
    echo 'Données sauvegardées.'; 

     mail($to, $subject, $message, $headers);
    // on affiche le résultat pour le visiteur
    echo 'Le mail a été envoyé.'; 
    }

// on ferme la connexion à mysql
mysql_close();
?> 