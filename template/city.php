<?php require_once('header.php'); ?>

<div class="container-fluid">

  <div class="row">
    <!!--STATS nd MAP--!>
    <div class="col-md-6">
      <!--Stats--!>
      <div id="stats" class="stats">
        <h3 class="stat-item">Currently Tracking <span class="stat-count">3451</span> people </h3>
        <hr />
        <div class="row">
          <div class="col-md-6">
            <p class="stat-item">Safe <span class="stat-count">30</span></p>
            <p class="stat-item">Injured <span class="stat-count">4</span></p>
            <p class="stat-item">Deceased <span class="stat-count">0</span></p>
          </div>
          <div class="col-md-6">
            <p class="stat-item">Located <span class="stat-count">200</span></p>
            <p class="stat-item">Missing <span class="stat-count">10</span></p>
          </div>
        </div>
      </div>
      <!--Stats--!>

      <div id="map-canvas">
      </div>
      
      <!--Search Form--!>
      <div id="search">
        <p class="lead">Search</p>
        <br /> 
        <div class="row">
          <div class="col-md-12">
            <div class="input-group">
              <input type="text" placeholder="Search for city" class="form-control">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default">Go!</button>
              </span>
            </div>
            <br />
            <div class="input-group">
              <input type="text" placeholder="Search for person" class="form-control">
              <span class="input-group-btn">
                <button type="button" class="btn btn-default">Go!</button>
              </span>
            </div>

          </div>
        </div>
      
      </div>
      <!--Search Form--!>
      
    </div>
    <!--STATS nd MAP--!>
    
    <!--CITY DATA--!>
    <div class="col-md-6">
       
      <!--City Header--!>
      <h3 class="page-header">Kathmandu</h3>

      <div class="row">
        <div class="col-md-6">
          <div class="stats-sub">
            <h5 class="stat-item">Tracking <span class="stat-count-sub">3451</span> people </h5>
            <hr>
            <div class="row">
              <div class="col-md-6">
                <p class="stat-item-sub">Safe <span class="stat-count-sub">30</span></p>
                <p class="stat-item-sub">Injured <span class="stat-count-sub">4</span></p>
                <p class="stat-item-sub">Deceased <span class="stat-count-sub">0</span></p>
              </div>
              <div class="col-md-6">
                <p class="stat-item-sub">Located <span class="stat-count-sub">200</span></p>
                <p class="stat-item-sub">Missing <span class="stat-count-sub">10</span></p>
              </div>
            </div>                                                                                                                                           
          </div>
        </div>

        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>Information</h4>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla massa leo, hendrerit non elementum quis, congue eu neque. Praesent a egestas dui, et commodo dolor. 
              Donec consequat pulvinar ornare. Nunc nec nisi vel urna dignissim condimentum.</p>

            </div>
          </div>
        </div>


      </div>
      <!--City Header--!>

      <h4 class="page-header">Recent Updates</h4>

      <?php for($i=1;$i<=3;$i++){ ?>
      <div class="update-item">
          <h5>Maecenas lectus urna, aliquam quis ullamcorpe <br /><span class="small text-muted">28 April 2015 @ 13:04 . author . 54m ago</span></h5>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla massa leo, hendrerit non elementum quis, congue eu neque. Praesent a egestas dui, et commodo dolor. 
          Donec consequat pulvinar ornare. Nunc nec nisi vel urna dignissim condimentum. Sed efficitur pulvinar congue.</p>
      </div>
      <?php } ?>

    </div>
    <!--CITY DATA--!>

  </div>

</div>


<?php require_once('footer.php');  ?>
