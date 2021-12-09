<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=test', "root", "");


} catch (PDOException $e) {
    print "Hata!: " . $e->getMessage() . "<br/>";
    exit();
}
$tarih = $_POST["paylasilma_tarihi"];

$sorgu = $dbh->prepare("SELECT * FROM bmproduction WHERE paylasilma_tarihi LIKE :paylasilma_tarihi");
$sorgu->execute(array("paylasilma_tarihi"=>"%".$tarih."%")); //buraya $tarihi çekicez
$veriler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
$sayfa =  json_encode($veriler);
print_r($sayfa);