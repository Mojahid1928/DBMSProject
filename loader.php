<style>
  #loader {
    position: fixed;
    width: 100%;
    height: 100vh;
    background-color: #fff;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #loader img {
    width: 20%;
  }
</style>
<div id="loader">
  <img src="Photos/loader.gif>
</div>

<script>
  let loader = document.getElementById("loader");

  function preloader() {
    loader.style.display = 'none';
  }
</script>