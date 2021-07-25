<?php
	include "baglan.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<?php
			if($_SERVER["REQUEST_METHOD"]=="POST")
		{
			//Güncellenenleri veri tabanına kaydetme
			if (isset($_POST["kayit_id"]))
				{
					$sorgu = "UPDATE `crout` SET  
								`kullanici_adi` = '".$_POST["kullanici_adi"]."', 
								`sifre` = '".$_POST["sifre"]."', 
								`eposta` = '".$_POST["eposta"]."', 
								`sehir` = '".$_POST["sehir"]."', 
								`sart_kabul` = '".((isset($_POST["sart_kabul"]))?'1':'0')."', 
								`odeme_yontemi` = '".((isset($_POST["odeme_yontemi"]))?$_POST["odeme_yontemi"]:"")."' 
							WHERE `crout`.`idcrout` = ".$_POST["kayit_id"];
					$baglanti->query($sorgu);
					echo "Kayıt başarıyla güncellendi...";
				}
				else
				{
					//Forma girilen bilgileri veri tabanına kaydetme
					$sorgu="INSERT INTO `crout` (`idcrout`, `kullanici_adi`, `sifre`, `eposta`, `sehir`, `sart_kabul`, `odeme_yontemi`, `fotograf`) 
					VALUES (NULL, 
					'".$_POST["kullanici_adi"]."', 
					'".$_POST["sifre"]."', 
					'".$_POST["eposta"]."', 
					'".$_POST["sehir"]."', 
					'".((isset($_POST["sart_kabul"]))?"1":"0")."', 
					'".((isset($_POST["odeme_yontemi"]))?$_POST["odeme_yontemi"]:" ")."',
					NULL)";
			
					$baglanti->query($sorgu);
					echo "Kayıt başarı ile oluşturuldu";
			}
		}
		?>
		<?php
			if(isset($_GET["sil"]))
			{
				$sorgu="DELETE FROM `crout` WHERE `crout`.`idcrout` = ".$_GET["sil"];
				$sonuc = $baglanti->query($sorgu);
				echo "Kayıt Silindi";
			}
		?>
		<table class="table table-hover">
			<tr>
				<th> # </th>
				<th> Kullanıcı Adı </th>
				<th> Şifre </th>
				<th> E Posta </th>
				<th> Şehir </th>
				<th> Şart Kabul </th>
				<th> Ödeme Yöntemi </th>
				<th> İşlemler </th>
			</tr>

			<?php
				$sorgu="SELECT * FROM `crout`";
				$sonuc = $baglanti->query($sorgu);
				if ($sonuc->num_rows > 0)
				{
					$sayac=0;
					while($satir = $sonuc->fetch_assoc())
					{
						$sayac++;
						?>
						<tr>
							<td> <?=$sayac?> </td>
							<td> <?=$satir["kullanici_adi"]?> </td>
							<td> <?=$satir["sifre"]?> </td>
							<td> <?=$satir["eposta"]?> </td>
							<td> 
								<?php
								if ($satir["sehir"] !=""){
									$sorgu="SELECT * FROM `sehir` WHERE `idsehir` = ".$satir["sehir"]; 
									$sehir = $baglanti->query($sorgu);
									$satir_sehir = $sehir ->fetch_assoc();
									echo $satir_sehir["sehir"];
								}
								?>
							</td>
							<td> <?=($satir["sart_kabul"])?"Evet":"Hayır"?> </td>
							<td> <?=(isset(($satir["odeme_yontemi"]))?$satir["odeme_yontemi"]:" ")?> </td>
							<td> 
								<a href="crud.php?sil=<?=$satir["idcrout"]?>" type="button" class="btn btn-danger">Sil</a>
								<a href="crud.php?duzenle=<?=$satir["idcrout"]?>" type="button" class="btn btn-primary">Düzenle</a>
							</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
	<?php
		if(isset($_GET["duzenle"]))
		{
			$sorgu = "SELECT * FROM `crout` WHERE `idcrout` = ".$_GET["duzenle"];
			$kayit_islem = $baglanti->query($sorgu);
			$kayit = $kayit_islem->fetch_assoc();
	?>
	<!-- düzenlenecek form sayfası ve elemanları -->
	<div class="container">
		<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data"  class="needs-validation" novalidate>
				<input type="hidden" name="kayit_id" value="<?=$kayit["idcrout"]?>"><!-- düzenleme için önemli -->
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						  <div class="row g-3">
							<div class="col-12">
							 <label for="username" class="form-label" >
							  Kullanıcı Adı 
							  </label>
							  <div class="input-group has-validation">
								<span class="input-group-text">@</span>
								<input type="text" class="form-control" name="kullanici_adi" value="<?=$kayit["kullanici_adi"]?>">
							  </div>
							  <div class="valid-feedback">
								  Looks good!
								</div>
							</div>
							
							<div class="col-6">
							<label class="form-label">Şifre <span class="text-muted"></span></label>
							  <input type="password" class="form-control" name="sifre" value="<?=$kayit["sifre"]?>">
							</div>

							<div class="col-12">
							  <label class="form-label">E-Posta <span class="text-muted"></span></label>
							  <input type="email" class="form-control" name="eposta" value="<?=$kayit["eposta"]?>">
							</div>

							<div class="col-md-12">
							  <label class="form-label">Şehir</label>
							  <select class="form-select" name="sehir">
							  <option value="">Seçiniz...</option>
							  <?php 
								$sorgu = "SELECT * FROM `sehir` ORDER BY `sehir`.`sehir` ASC";//alfabetik sıralar
									$sonuc = $baglanti->query($sorgu);
									if ($sonuc->num_rows > 0) {
										while($satir = $sonuc->fetch_assoc()) {
											//echo '<option value="'.$satir["idsehir"].'">'.$satir["sehir"].'</option>';
											if ($satir["idsehir"] == $kayit["sehir"])
												echo '<option value="'.$satir["idsehir"].'" selected>'.$satir["sehir"].'</option>';
											else
												echo '<option value="'.$satir["idsehir"].'">'.$satir["sehir"].'</option>';
										}
										
									}
							  ?>
							  </select>

							</div>


							<hr class="my-4">

							<div class="form-check">
								<input type="checkbox" class="form-check-input" name="sart_kabul" <?=($kayit["sart_kabul"])?"checked":" "?>>
								<label class="form-check-label">Tüm şartları kabul ediyorum</label>
							</div>
          <hr class="my-4">

							<h4 class="mb-3">Ödeme Yöntemi</h4>

							<div class="my-3">
								<div class="form-check">
									<input name="odeme_yontemi" value="kredikarti" type="radio" class="form-check-input" <?=($kayit["odeme_yontemi"]=="kredikarti")?"checked":" "?>>
									<label class="form-check-label">Kredi Kartı</label>
								</div>
								<div class="form-check">
									<input name="odeme_yontemi" value="havale" type="radio" class="form-check-input" <?=($kayit["odeme_yontemi"]=="havale")?"checked":" "?>>
									<label class="form-check-label">Havale</label>
								</div>
								<div class="form-check">
									<input name="odeme_yontemi" value="kriptopara" type="radio" class="form-check-input" <?=($kayit["odeme_yontemi"]=="kriptopara")?"checked":" "?>>
									<label class="form-check-label">Kripto Para</label>
								</div>
							</div>
								<div class="col-md-4">
									<label class="form-label">Fotoğraf seçiniz</label>
									<input class="form-control" type="file" name="fotograf">
								</div>
							</div>


          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" type="submit">Güncelle</button>
        </form>
		<?php
		}
		else
		{
		?>
		<!-- yeni form sayfası ve elemanları -->
		<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data"  class="needs-validation" novalidate>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						  <div class="row g-3">
							<div class="col-12">
							 <label for="username" class="form-label" >
							  Kullanıcı Adı 
							  </label>
							  <div class="input-group has-validation">
								<span class="input-group-text">@</span>
								<input type="text" class="form-control" name="kullanici_adi" >
							  </div>
							  <div class="valid-feedback">
								  Looks good!
								</div>
							</div>
							
							<div class="col-6">
							<label class="form-label">Şifre <span class="text-muted"></span></label>
							  <input type="password" class="form-control" name="sifre" >
							</div>

							<div class="col-12">
							  <label class="form-label">E-Posta <span class="text-muted"></span></label>
							  <input type="email" class="form-control" name="eposta" >
							</div>

							<div class="col-md-12">
							  <label class="form-label">Şehir</label>
							  <select class="form-select" name="sehir">
							  <option value="">Seçiniz...</option>
							  <?php 
								$sorgu = "SELECT * FROM `sehir` ORDER BY `sehir`.`sehir` ASC";
									$sonuc = $baglanti->query($sorgu);
									if ($sonuc->num_rows > 0) {
										while($satir = $sonuc->fetch_assoc()) {

											echo '<option value="'.$satir["idsehir"].'">'.$satir["sehir"].'</option>';
										}
									}
							  ?>
							  </select>

							</div>


							<hr class="my-4">

							<div class="form-check">
								<input type="checkbox" class="form-check-input" name="sart_kabul" >
								<label class="form-check-label">Tüm şartları kabul ediyorum</label>
							</div>
          <hr class="my-4">

							<h4 class="mb-3">Ödeme Yöntemi</h4>

							<div class="my-3">
								<div class="form-check">
									<input name="odeme_yontemi" value="kredikarti" type="radio" class="form-check-input" >
									<label class="form-check-label">Kredi Kartı</label>
								</div>
								<div class="form-check">
									<input name="odeme_yontemi" value="havale" type="radio" class="form-check-input" >
									<label class="form-check-label">Havale</label>
								</div>
								<div class="form-check">
									<input name="odeme_yontemi" value="kriptopara" type="radio" class="form-check-input" >
									<label class="form-check-label">Kripto Para</label>
								</div>
							</div>
								<div class="col-md-4">
									<label class="form-label">Fotoğraf seçiniz</label>
									<input class="form-control" type="file" name="fotograf">
								</div>
							</div>


          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" type="submit">Kaydet</button>
        </form>
		<?php
			}
		?>
	</div>
	</body>
</html>