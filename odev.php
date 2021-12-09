<?php

try {
    $dbh = new PDO('mysql:host=localhost;dbname=test', "root", "");


} catch (PDOException $e) {
    print "Hata!: " . $e->getMessage() . "<br/>";
    exit();
}
$veriler = array(array("sarki"=>"Belki","sanatci"=>"Canberk","dinlenme_sayisi"=>rand(200,1000000),"paylasilma_tarihi"=>"2021-09-02"),
      array("sarki"=>"Aksamci","sanatci"=>"Emircan","dinlenme_sayisi"=>rand(200,1000000),"paylasilma_tarihi"=>"2020-10-10"),
      array("sarki"=>"Mammamia","sanatci"=>"Maneskin","dinlenme_sayisi"=>rand(200,1000000),"paylasilma_tarihi"=>"2020-02-14"),
      array("sarki"=>"BoteMarsi","sanatci"=>"GulcanHoca","dinlenme_sayisi"=>rand(200,1000000),"paylasilma_tarihi"=>"2021-11-08"),
      array("sarki"=>"SarilirimBirine","sanatci"=>"Adamlar","dinlenme_sayisi"=>rand(200,10000),"paylasilma_tarihi"=>"2018-08-21"),
      array("sarki"=>"AcininIlaci","sanatci"=>"Adamlar","dinlenme_sayisi"=>rand(200,1000000),"paylasilma_tarihi"=>"2019-09-12")
);
foreach ($veriler as $k){
    $sorgu = $dbh->prepare("INSERT INTO bmproduction (sarki, sanatci, dinlenme_sayisi, paylasilma_tarihi) VALUES (:sarki, :sanatci, :dinlenme_sayisi, :paylasilma_tarihi)");
    $sorgu->execute(array("sarki"=>$k["sarki"],"sanatci"=>$k["sanatci"],"dinlenme_sayisi"=>$k["dinlenme_sayisi"],"paylasilma_tarihi"=>$k["paylasilma_tarihi"]));
}

$ch = curl_init();//istenilen yıldaki değerleri çekmek için post kullandım.
curl_setopt_array($ch, array(
    CURLOPT_URL => "http://localhost/odevjson.php",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => "paylasilma_tarihi=2021",
    CURLOPT_RETURNTRANSFER => true
));
$gelenler = curl_exec($ch);
curl_close($ch);
$sayfa = json_decode($gelenler,true);
$sayi = [];

echo "SARKILAR";
echo "<hr>";
foreach($sayfa as $l){ // O yıldaki şarkılar hakkında kaç gün önce çıktığı ve kaç kez dinlendiği bilgilerini yazdırdım.
    $bugün = date("d.m.Y");
    $simdi = strtotime($bugün);
    $sarkizaman = strtotime($l["paylasilma_tarihi"]);
    echo $l["sanatci"]."-".$l["sarki"]." ".round(($simdi - $sarkizaman) / (86400))." Gün önce çıkmış"." ve ".$l["dinlenme_sayisi"]." kez dinlenmiş."."<br>";
    array_push($sayi, $l["dinlenme_sayisi"]);
}

$tutar = 0;// Fonksyion kullanarak o şarkıların kazançlarını hesaplattım. (1 dinlenme = 10 krş)
function kazanc($dinlenme){
    $tutar = 0;
    $tutar += $dinlenme/10;
    return $tutar;
}
$toplam =0;
echo "KAZANC";
echo "<hr>";
foreach($sayi as $l){

    echo "Şirketin 2021 yılında çıkardığı şarkılardan kazanclari = ".kazanc($l)." Türk Lirası"."<br>";
    $toplam += kazanc($l);
}echo "Şirketin 2021 Yılındaki Toplam Kazancı : ".$toplam."Türk Lırası"."<br>";

// Şirketin hedef kazancı 500k ile 1m arasında. Bu duruma göre gelir durumunu yazdırdım.

if($toplam <50000)echo"Şirket Bu Sezon Beklenenden Az Gelir Elde Etmiş.";
else if($toplam<100000)echo "Şirket Bu Sezon Normal Düzeyde Gelir Elde Etmiştir";
else echo "Şirket Bu Sezon Üst Düzey Gelir Elde Etmiştir";

//--------------------------------------------------
