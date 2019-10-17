<?php
    //cek session
    if(empty($_SESSION['admin'])){
        $_SESSION['err'] = '<center>Anda harus login terlebih dahulu!</center>';
        header("Location: ./");
        die();
    } else {

        if($_SESSION['admin'] != 1 AND $_SESSION['admin'] != 2){
            echo '<script language="javascript">
                    window.alert("ERROR! Anda tidak memiliki hak akses untuk membuka halaman ini");
                    window.location.href="./logout.php";
                  </script>';
        } else {

            if(isset($_REQUEST['sub'])){
                $sub = $_REQUEST['sub'];
                switch ($sub) {
                    case 'back':
                        include "backup.php";
                        break;
                    case 'rest':
                        include "restore.php";
                        break;
                    case 'usr':
                        include "user.php";
                        break;
                    }
            } else {

                if(isset($_REQUEST['submit'])){

                    //validasi form kosong
                    if ($_REQUEST['nama_dinas'] == "" || $_REQUEST['alamat_dinas'] == "" || $_REQUEST['nm_bagian'] == "" || $_REQUEST['nm_kep_bagian'] == "" || $_REQUEST['nama_admin'] == "" || $_REQUEST['nip_kep_bagian'] == ""
                        || $_REQUEST['website'] == "" || $_REQUEST['email_dinas'] == ""){
                        $_SESSION['errEmpty'] = 'ERROR! Semua form wajib diisi';
                        header("Location: ././admin.php?page=sett");
                        die();
                    } else {

                        $id_instansi = "1";
                        $nama_dinas = $_REQUEST['nama_dinas'];
                        $alamat_dinas = $_REQUEST['alamat_dinas'];
                        $nm_bagian = $_REQUEST['nm_bagian'];
                        $nama_admin = $_REQUEST['nama_admin'];
                        $nm_kep_bagian = $_REQUEST['nm_kep_bagian'];
                        $nip_kep_bagian = $_REQUEST['nip_kep_bagian'];
                        $website = $_REQUEST['website'];
                        $email_dinas = $_REQUEST['email_dinas'];
                        $id_user = $_SESSION['id_user'];

                        //validasi input data
                        if(!preg_match("/^[a-zA-Z0-9. -]*$/", $nama_dinas)){
                            $_SESSION['nama_dinas'] = 'Form Nama Dinas hanya boleh mengandung karakter huruf, angka, spasi, titik(.) dan minus(-)';
                            echo '<script language="javascript">window.history.back();</script>';
                        } else {

                            if(!preg_match("/^[a-zA-Z0-9. -]*$/", $nm_bagian)){
                                $_SESSION['nm_bagian'] = 'Form Nama Bidang hanya boleh mengandung karakter huruf, angka, spasi, titik(.) dan minus(-)';
                                echo '<script language="javascript">window.history.back();</script>';
                            } else {

                                if(!preg_match("/^[a-zA-Z0-9.,:\/<> -\"]*$/", $alamat_dinas)){
                                    $_SESSION['alamat_dinas'] = 'Form Alamat Dinas hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), titik dua(:), petik dua(""), garis miring(/) dan minus(-)';
                                    echo '<script language="javascript">window.history.back();</script>';
                                } else {

                                    if(!preg_match("/^[a-zA-Z0-9.,()\/ -]*$/", $nm_kep_bagian)){
                                        $_SESSION['alamat'] = 'Form Alamat hanya boleh mengandung karakter huruf, angka, spasi, titik(.), koma(,), minus(-), garis miring(/), dan kurung()';
                                        echo '<script language="javascript">window.history.back();</script>';
                                    } else {

                                        if(!preg_match("/^[a-zA-Z., ]*$/", $nama_admin)){
                                            $_SESSION['nama_admin'] = 'Form Nama Administrator hanya boleh mengandung karakter huruf, spasi, titik(.) dan koma(,)<br/><br/>';
                                            echo '<script language="javascript">window.history.back();</script>';
                                        } else {

                                            if(!preg_match("/^[0-9 -]*$/", $nip_kep_bagian)){
                                                $_SESSION['nip_kep_dinas'] = 'Form NIP Kepala Bidang hanya boleh mengandung karakter angka, spasi, dan minus(-)<br/><br/>';
                                                echo '<script language="javascript">window.history.back();</script>';
                                            } else {

                                                //validasi url website
                                                if(!filter_var($website, FILTER_VALIDATE_URL)){
                                                    $_SESSION['website'] = 'Format URL Website tidak valid';
                                                    header("Location: ././admin.php?page=sett");
                                                    die();
                                                } else {

                                                    //validasi email
                                                    if(!filter_var($email_dinas, FILTER_VALIDATE_EMAIL)){
                                                        $_SESSION['email_dinas'] = 'Format Email tidak valid';
                                                        header("Location: ././admin.php?page=sett");
                                                        die();
                                                    } else {

                                                        $ekstensi = array('png','jpg');
                                                        $logo = $_FILES['logo']['name'];
                                                        $x = explode('.', $logo);
                                                        $eks = strtolower(end($x));
                                                        $ukuran = $_FILES['logo']['size'];
                                                        $target_dir = "upload/";

                                                        if (! is_dir($target_dir)) {
                                                            mkdir($target_dir, 0755, true);
                                                        }

                                                        //jika form logo tidak kosong akan mengeksekusi script dibawah ini
                                                        if(!empty($logo)){

                                                            $nlogo = $logo;
                                                            //validasi gambar
                                                            if(in_array($eks, $ekstensi) == true){
                                                                if($ukuran < 2000000){

                                                                    $query = mysqli_query($config, "SELECT logo FROM tbl_instansi");
                                                                    list($logo) = mysqli_fetch_array($query);

                                                                    unlink($target_dir.$logo);

                                                                    move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir.$nlogo);

                                                                    $query = mysqli_query($config, "UPDATE tbl_instansi SET nama_dinas='$nama_dinas',nama_admin='$nama_admin',nm_bagian='$nm_bagian',alamat_dinas='$alamat_dinas',nm_kep_bagian='$nm_kep_bagian',nip_kep_bagian='$nip_kep_bagian',website='$website',email_dinas='$email_dinas',logo='$nlogo',id_user='$id_user' WHERE id_instansi='$id_instansi'");

                                                                    if($query == true){
                                                                        $_SESSION['succEdit'] = 'SUKSES! Data instansi berhasil diupdate';
                                                                        header("Location: ././admin.php?page=sett");
                                                                        die();
                                                                    } else {
                                                                        $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                                        echo '<script language="javascript">window.history.back();</script>';
                                                                    }
                                                                } else {
                                                                    $_SESSION['errSize'] = 'Ukuran file yang diupload terlalu besar!<br/><br/>';
                                                                    echo '<script language="javascript">window.history.back();</script>';
                                                                }
                                                            } else {
                                                                $_SESSION['errSize'] = 'Format file gambar yang diperbolehkan hanya *.JPG dan *.PNG!<br/><br/>';
                                                                echo '<script language="javascript">window.history.back();</script>';
                                                            }
                                                        } else {

                                                            //jika form logo kosong akan mengeksekusi script dibawah ini
                                                            $query = mysqli_query($config, "UPDATE tbl_instansi SET nama_dinas='$nama_dinas',nama_admin='$nama_admin',nm_bagian='$nm_bagian',alamat_dinas='$alamat_dinas',nm_kep_bagian='$nm_kep_bagian',nip_kep_bagian='$nip_kep_bagian',website='$website',email_dinas='$email_dinas',id_user='$id_user' WHERE id_instansi='$id_instansi'");

                                                            if($query == true){
                                                                $_SESSION['succEdit'] = 'SUKSES! Data instansi berhasil diupdate';
                                                                header("Location: ././admin.php?page=sett");
                                                                die();
                                                            } else {
                                                                $_SESSION['errQ'] = 'ERROR! Ada masalah dengan query';
                                                                echo '<script language="javascript">window.history.back();</script>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {

                    $query = mysqli_query($config, "SELECT * FROM tbl_instansi");
                    if(mysqli_num_rows($query) > 0){
                        $no = 1;
                        while($row = mysqli_fetch_array($query)){?>

                        <!-- Row Start -->
                        <div class="row">
                            <!-- Secondary Nav START -->
                            <div class="col s12">
                                <nav class="secondary-nav">
                                    <div class="nav-wrapper blue-grey darken-1">
                                        <ul class="left">
                                            <li class="waves-effect waves-light"><a href="?page=sett" class="judul"><i class="material-icons"></i><strong> Manajemen Instansi</strong></a></li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>
                            <!-- Secondary Nav END -->
                        </div>
                        <!-- Row END -->

                        <?php
                            if(isset($_SESSION['errEmpty'])){
                                $errEmpty = $_SESSION['errEmpty'];
                                echo '<div id="alert-message" class="row">
                                        <div class="col m12">
                                            <div class="card red lighten-5">
                                                <div class="card-content notif">
                                                    <span class="card-title red-text"><i class="material-icons md-36">clear</i> '.$errEmpty.'</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                unset($_SESSION['errEmpty']);
                            }
                            if(isset($_SESSION['succEdit'])){
                                $succEdit = $_SESSION['succEdit'];
                                echo '<div id="alert-message" class="row">
                                        <div class="col m12">
                                            <div class="card green lighten-5">
                                                <div class="card-content notif">
                                                    <span class="card-title green-text"><i class="material-icons md-36">done</i> '.$succEdit.'</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                unset($_SESSION['succEdit']);
                            }
                            if(isset($_SESSION['errQ'])){
                                $errQ = $_SESSION['errQ'];
                                echo '<div id="alert-message" class="row">
                                        <div class="col m12">
                                            <div class="card red lighten-5">
                                                <div class="card-content notif">
                                                    <span class="card-title red-text"><i class="material-icons md-36">clear</i> '.$errQ.'</span>
                                            </div>
                                            </div>
                                        </div>
                                    </div>';
                                unset($_SESSION['errQ']);
                            }
                        ?>

                        <!-- Row form Start -->
                        <div class="row jarak-form">

                            <!-- Form START -->
                            <form class="col s12" method="post" action="?page=sett" enctype="multipart/form-data">

                                <!-- Row in form START -->
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input type="hidden" value="<?php echo $id_instansi; ?>" name="id_instansi">
                                        <i class="material-icons prefix md-prefix">school</i>
                                        <input id="nama_dinas" type="text" class="validate" name="nama_dinas" value="<?php echo $row['nama_dinas']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['nama_dinas'])){
                                                    $nama_dinas = $_SESSION['nama_dinas'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nama_dinas.'</div>';
                                                    unset($_SESSION['nama_dinas']);
                                                }
                                            ?>
                                        <label for="nama_dinas"><strong>Nama Dinas</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">work</i>
                                        <input id="nm_bagian" type="text" class="validate" name="nm_bagian" value="<?php echo $row['nm_bagian']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['nm_bagian'])){
                                                    $nm_bagian = $_SESSION['nm_bagian'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nm_bagian.'</div>';
                                                    unset($_SESSION['nm_bagian']);
                                                }
                                            ?>
                                        <label for="nm_bagian"><strong>Nama Bidang</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">place</i>
                                        <input id="alamat_dinas" type="text" class="validate" name="alamat_dinas" value='<?php echo $row['alamat_dinas']; ?>' required>
                                            <?php
                                                if(isset($_SESSION['alamat_dinas'])){
                                                    $alamat_dinas = $_SESSION['alamat_dinas'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$alamat_dinas.'</div>';
                                                    unset($_SESSION['alamat_dinas']);
                                                }
                                            ?>
                                        <label for="alamat_dinas"><strong>Alamat Dinas</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">account_box</i>
                                        <input id="nm_kep_bagian" type="text" class="validate" name="nm_kep_bagian" value="<?php echo $row['nm_kep_bagian']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['nm_kep_bagian'])){
                                                    $nm_kep_bagian = $_SESSION['nm_kep_bagian'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nm_kep_bagian.'</div>';
                                                    unset($_SESSION['nm_kep_bagian']);
                                                }
                                            ?>
                                        <label for="nm_kep_bagian"><strong>Nama Kepala Bidang</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">mail</i>
                                        <input id="email_dinas" type="text" class="validate" name="email_dinas" value="<?php echo $row['email_dinas']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['email_dinas'])){
                                                    $email_dinas = $_SESSION['email_dinas'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$email_dinas.'</div>';
                                                    unset($_SESSION['email_dinas']);
                                                }
                                            ?>
                                        <label for="email_dinas"><strong>Email Dinas</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">looks_one</i>
                                        <input id="nip_kep_bagian" type="text" class="validate" name="nip_kep_bagian" value="<?php echo $row['nip_kep_bagian']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['nip_kep_bagian'])){
                                                    $nip_kep_bagian = $_SESSION['nip_kep_bagian'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nip_kep_bagian.'</div>';
                                                    unset($_SESSION['nip_kep_bagian']);
                                                }
                                            ?>
                                        <label for="nip_kep_bagian"><strong>NIP. Kepala Bidang</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">language</i>
                                        <input id="website" type="url" class="validate" name="website" value="<?php echo $row['website']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['website'])){
                                                    $website = $_SESSION['website'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$website.'</div>';
                                                    unset($_SESSION['website']);
                                                }
                                            ?>
                                        <label for="website"><strong>Website</strong></label>
                                    </div>
                                    <div class="input-field col s6">
                                        <i class="material-icons prefix md-prefix">people</i>
                                        <input id="nama_admin" type="text" class="validate" name="nama_admin" value="<?php echo $row['nama_admin']; ?>" required>
                                            <?php
                                                if(isset($_SESSION['nama_admin'])){
                                                    $nama_admin = $_SESSION['nama_admin'];
                                                    echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$nama_admin.'</div>';
                                                    unset($_SESSION['nama_admin']);
                                                }
                                            ?>
                                        <label for="nama_admin"><strong>Nama Administator</strong></label>
                                    </div>
                                    <div class="input-field col s6 tooltipped" data-position="top" data-tooltip="Jika tidak ada logo, biarkan kosong">
                                        <div class="file-field input-field">
                                            <div class="btn light-green darken-1">
                                                <span>File</span>
                                                <input type="file" id="logo" name="logo">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text" placeholder="Upload Logo instansi">
                                            </div>
                                                <?php
                                                    if(isset($_SESSION['errSize'])){
                                                        $errSize = $_SESSION['errSize'];
                                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$errSize.'</div>';
                                                        unset($_SESSION['errSize']);
                                                    }
                                                    if(isset($_SESSION['errFormat'])){
                                                        $errFormat = $_SESSION['errFormat'];
                                                        echo '<div id="alert-message" class="callout bottom z-depth-1 red lighten-4 red-text">'.$errFormat.'</div>';
                                                        unset($_SESSION['errFormat']);
                                                    }
                                                ?>
                                            <small class="red-text">*Format file yang diperbolehkan hanya *.JPG, *.PNG dan ukuran maksimal file 2 MB. Disarankan gambar berbentuk kotak atau lingkaran!</small>
                                        </div>
                                    </div>
                                    <div class="input-field col s6">
                                        <img class="logo" src="upload/<?php echo $row['logo']; ?>"/>
                                    </div>
                                </div>
                                <!-- Row in form END -->

                                <div class="row">
                                    <div class="col 6">
                                        <button type="submit" name="submit" class="btn-large blue waves-effect waves-light">SIMPAN <i class="material-icons">done</i></button>
                                    </div>
                                    <div class="col 6">
                                        <a href="./admin.php" class="btn-large deep-orange waves-effect waves-light">BATAL <i class="material-icons">clear</i></a>
                                    </div>
                                </div>

                            </form>
                            <!-- Form END -->

                        </div>
                        <!-- Row form END -->

<?php
                        }
                    }
                }
            }
        }
    }
?>
