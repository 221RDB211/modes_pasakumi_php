<?php
session_start();  // Sākam sesiju

// Izdzēst visus sesijas mainīgos
session_unset();

// Izdzēst pašu sesiju
session_destroy();

// Pāradresēt uz sākumlapu
header("Location: index.php");
exit();
?>
