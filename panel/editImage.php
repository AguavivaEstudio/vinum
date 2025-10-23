<?php
ob_start();
include '_includes.php';

$sSql = "SELECT `key`, `value` FROM `sys_config`;";
$result = ExecuteSql($sSql, null);

$_SESSION['sysConfig'] = array();
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $_SESSION['sysConfig'][$row['key']] = $row['value'];
}

CreateHeadder();
?>
<body>
<h1>TEST Image Cropper</h1>

<link  href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<style>
  img {
    max-width: 100%;
  }
  #image {
    max-width: 600px;
  }
  #result img {
    border: 1px solid #ddd;
    margin-top: 10px;
    max-width: 600px;
  }
</style>

<p>
  <input type="file" id="fileInput" accept="image/*">
  <button id="btnCrop">Crop</button>
  <button id="btnRestore">Restore</button>
</p>

<img id="image" src="/cropperjs/picture.jpg" alt="Picture">
<div id="result"></div>

<script>
let cropper;
const image = document.getElementById('image');
const fileInput = document.getElementById('fileInput');
const result = document.getElementById('result');
const btnCrop = document.getElementById('btnCrop');
const btnRestore = document.getElementById('btnRestore');

// Initialize Cropper
cropper = new Cropper(image, {
  aspectRatio: 16 / 9,
  viewMode: 1
});

// Handle new file input
fileInput.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file || !file.type.startsWith('image/')) {
    alert("Please select a valid image file.");
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    cropper.replace(reader.result);
  };
  reader.readAsDataURL(file);
});

// Crop
btnCrop.addEventListener('click', () => {
  const canvas = cropper.getCroppedCanvas();
  if (canvas) {
    const img = document.createElement('img');
    img.src = canvas.toDataURL('image/png');
    result.innerHTML = '';
    result.appendChild(img);
  }
});

// Restore
btnRestore.addEventListener('click', () => {
  cropper.reset();
  result.innerHTML = '';
});
</script>

</body>
</html>
