<?php
	/* error_reporting(E_ALL & ~(E_NOTICE|E_WARNING)); */
	/* header('Content-Type: text/html; charset=utf-8'); */
	/* Διαμορφώνεται η έξοδος της php ώστε να μπορεί να εμφανίσει και τους Ελληνικούς χαρακτήρες, 
	για τη json μορφή είναι προτιμότερο το παρακάτω */
	header('Content-Type: application/json; charset=UTF-8');
	
class BaseController {
	
	public static function getBooks($db){

		/* Τρέχει ένα ερώτημα στη ΒΔ */
		$query = "SELECT * FROM books ORDER BY id ASC";
		$stmt = $db->prepare($query);
		$stmt->execute();
		/* $books = $stmt->fetch(); */
		/* Η εντολή fetch φέρνει μόνο μία γραμμή από το παραπάνω query */
		$books = $stmt->fetchAll();
		/* Η εντολή fetchAll φέρνει όλες τις γραμμές του πίνκακα */

		/*
		Παίρνει έναν πρώτο πίνακα με όλα τα βιβλία και τα αντιγράφει στο results
		*/
		$results = $books;
		/*
		print_r($results);
		*/

		/* Στο results θα μπουν επιπλέον πεδία */

		$i = 0; /* counter */
		/*
		Για κάθε βιβλίο θα ψάξει τις κατηγορίες του
		*/
		foreach( $books as $book ){
			/* Με ενα join πινάκων θα πάρει όλα τα λεκτικά των κατηγοριών στις οποίες ανήκει το βιβλίο */
			/* $query = "SELECT c.`name` FROM book_category_ref AS bc JOIN categories AS c ON c.`cat_id`=c.`id` WHERE bc.`book_id`='".$book['id']."'";
			/* Αφαίρεσα μερικά αυτάκια μονά - έχει και λαθάκια στο alias : όχι c.cat_id ΑΛΛΑ bc.cat_id */
			$query = "SELECT c.`name` FROM book_category_ref AS bc JOIN categories AS c ON bc.`cat_id` = c.id WHERE bc.`book_id` = " . $book['id'] . "";
			$stmt = $db->prepare($query);  /* Δεν είχε γραφτεί */
			$stmt->execute(); /* Έβγαλα το $query */
			$categories	= $stmt->fetchAll();

			$results[$i]['categories'] = $categories;
			/* Αποθηκεύει ένα πινακάκι με τα λεκτικά στο πεδίο [cetgories] που προστίθεται στα αρχικά πεδία του Array : results */
			/* 
			Το περιεχόμενο της στήλης [age_group] που είναι ένα text με τιμές που χωρίζονται από κόμματα (,) 
			γεμίζει έναν πίνακα
			*/
			$age_groups = explode(",", $book['age_group']);
			/* Για κάθε στοιχείο του πίνακα... */
			foreach( $age_groups as $age_group ){				
				/* $query = "SELECT title FROM edu_grades WHERE id='".$age_group."'"; */
				/*
				Τα μονά αυτάκια τα αποφεύγω επειδή το πεδίο που χτυπάω είναι αριθμός 
				Δεν υπάρχει πεδίο title - θα φέρω το name 
				(ίσως ήταν καλύτερα να αλλάξω το όνομα του πεδίου στη ΒΔ επειδή
				το λεκτικό name είναι συνήθως δεσμευμένη λέξη
				*/
				$query = "SELECT name FROM edu_grades WHERE id=" . $age_group . "";
				$stmt = $db->prepare($query);
				$stmt->execute();
				$ages = $stmt->fetch();
				/* ...γεμίζει έναν πίνακα με τα ηλικιακά groups για τα οποία είναι κατάλληλο */
				$results[$i]['ages'][] = $ages;
			}
			/* Για κάθε βιβλίο στο results προσαρτείται και ένα πινακάκι με τους συγγραφείς του */
			/* $query = "SELECT * FROM authors WHERE id = " . $books['author_id'].""; */
			/* Δε χρειάζονται όλα τα πεδία, Μόνο το Name, Όχι $books ~> $book['author_id'] */			
			$query = "SELECT name FROM authors WHERE id = " . $book['author_id']."";
	
			$stmt = $db->prepare($query);
			$stmt->execute();
			$author = $stmt->fetch();
			$results[$i]['author'] = $author;
			
			$i++;
		}

		/* print_r($results); */
		return $results;
		/*
		Εδώ έχει ένα μικρό τυπογραφικό λάθος
		Αν υποθέσουμε ότι έπρεπε να γράφει results
		Τότε επιστρέφει τον πίνακα
		*/
	}
}

// DB Credentials
$db_host = '';
$db_user = '';
$db_pass = '';
$db_name = '';

// DB Connection
$db = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
/* $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'); */

// Function result
$books = BaseController::getBooks($db);

/* Nomikos */
$jsonStr = json_encode($books, JSON_UNESCAPED_UNICODE);
echo $jsonStr;

?>
