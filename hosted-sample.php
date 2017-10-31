<?php 

if (isset($_POST['responseCode'])) {
	echo '<p><strong>Cardstream Response</strong></p>';
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
}

$action = "https://gateway.cardstream.com/hosted/";
$sig_key = "Circle4Take40Idea";

$fields = array(	
	"merchantID" => '100001', 
	"action" => "SALE",
	"type" => 1,
	"amount" => 1001,
	"transactionUnique" => uniqid(),
	"orderRef" => "Test purchase",
	"currencyCode" => 826,
	"countryCode" => 826,
	"redirectURL" => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 
);
	

function createSignature(array $data, $key, $algo = null) {

	$algos = array(
		'SHA512' => true,
		'SHA256' => true,
		'SHA1' => true,
		'MD5' => true,
		'CRC32' => true,
	);

	if ($algo === null) {
		$algo = 'SHA512';
	}
	
	ksort($data);

	// Create the URL encoded signature string
	$ret = http_build_query($data, '', '&');

	// Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
	$ret = preg_replace('/%0D%0A|%0A%0D|%0A|%0D/i', '%0A', $ret);
	
	// Hash the signature string and the key together
	$ret = hash($algo, $ret . $key);

	// Prefix the algorithm if not the default
	if ($algo !== 'SHA512') {
		$ret = '{' . $algo . '}' . $ret;
	}

	return $ret;	
}
?>

<form action="<?= $action ?>" method="post">
	
	<?php	foreach ($fields as $key => $value) { ?>
			<input type="hidden" name="<?= $key ?>" value="<?= $value ?>">			
	<?php	}
	
		if (isset($sig_key)) { ?>
			<input type="hidden" name="signature" value="<?= createSignature($fields, $sig_key, 'SHA512') ?>" />
	<?php	} ?>
	<input type="submit" value="Pay Now">
</form>
