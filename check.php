<?php
if(file_exists($_POST['filename'])){
    echo filesize($_POST['filename']);
}