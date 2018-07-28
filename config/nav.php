<nav class="navbar fixed-top navbar-expand-lg navbar-light" style="background-color:#E8F4F8;">
  <!-- <a class="navbar-brand" href="#">Navbar</a> -->
  <span class="navbar-brand" id="username-display">Welcome<span style="font-style:italic;"><?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])) echo " ".$_SESSION['username']; ?></span>!
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse justify-content-stretch" id="navbarToggler">
    <ul class="navbar-nav mt-2 mt-lg-0" id="navbarList">
      <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == "/index.php") echo 'active';?>">
        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
      </li>
      <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
      <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == "/add.php") echo 'active';?>">
        <a class="nav-link" href="add.php">Add POI</a>
      </li>
      <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == "/list.php") echo 'active';?>">
        <a class="nav-link" href="mypoi.php">MyPOI</a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
          <a class="nav-link" href="summary.pdf">About</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
      <li>
        <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
        <a class="nav-link" href="login/logout.php" style="color:blue;">Sign out</a>
        <?php else: ?>
        <a class="nav-link" href="login/login.php" style="color:blue;">Sign in</a>
        <?php endif; ?>
      </li>
    </ul>
    <!-- <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form> -->
  </div>
</nav>
