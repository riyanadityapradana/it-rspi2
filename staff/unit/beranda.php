<?php
// Halaman beranda staff
?>
<style>
  .fc .fc-day-today {
    background: #4caf50 !important;
    color: #fff !important;
    border-radius: 6px;
  }
</style>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>3</h3>
        <p>Usia Lebih Dari 60 th</p>
      </div>
      <div class="icon">
        <i class="fas fa-child"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>5</h3>
        <p>Usia Kurang Dari 60 th</p>
      </div>
      <div class="icon">
        <i class="fas fa-child"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>760</h3>
        <p>Sales</p>
      </div>
      <div class="icon">
        <i class="fas fa-child"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>2,000</h3>
        <p>New Members</p>
      </div>
      <div class="icon">
        <i class="fas fa-child"></i>
      </div>
    </div>
  </div>
</div>
<!-- <div class="card"> 
  <div class="card-body">
    <h3>Beranda Admin</h3>
    <p>Ini adalah halamansssSSS beranda admin.</p>
  </div>
</div>-->
<hr>
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h5 class="card-title">Grafik Statistik (Dummy)</h5></div>
      <div class="card-body">
        <canvas id="myChart" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><h5 class="card-title">Kalender</h5></div>
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Data Dummy',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'id',
    height: 400,
    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
    selectable: false,
    dateClick: function(info) {
      // Bisa tambahkan aksi jika tanggal diklik
    }
  });
  calendar.render();
});
</script> 