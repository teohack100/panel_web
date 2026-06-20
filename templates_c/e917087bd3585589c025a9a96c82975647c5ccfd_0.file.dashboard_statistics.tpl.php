<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:26
  from "C:\xampp\htdocs\panel_web\templates\js\dashboard_statistics.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21336650716_29217338',
  'file_dependency' => 
  array (
    'e917087bd3585589c025a9a96c82975647c5ccfd' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\js\\dashboard_statistics.tpl',
      1 => 1608703408,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21336650716_29217338 ($_smarty_tpl) {
echo '<script'; ?>
>
    /**
 * Theme: Metrica - Responsive Bootstrap 4 Admin Dashboard
 * Author: Mannatthemes
 * Dashboard Js
 */


  //colunm-1
  
var options = {
  chart: {
      height: 340,
      type: 'bar',
      toolbar: {
          show: false
      },
      dropShadow: {
        enabled: true,
        top: 0,
        left: 5,
        bottom: 5,
        right: 0,
        blur: 5,
        color: '#010f42',
        opacity: 0.35
    },
  },
  plotOptions: {
      bar: {
          horizontal: false,
          endingShape: 'rounded',
          columnWidth: '25%',
      },
  },
  dataLabels: {
      enabled: false
  },
 
  stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
  },
  colors: ["#2c77f4", "#1ecab8"],
  series: [{
      name: 'New Visitors',
      data: [68, 44, 55, 57, 56, 61, 58, 63, 60, 66]
  }, {
      name: 'Unique Visitors',
      data: [51, 76, 85, 101, 98, 87, 105, 91, 114, 94]
  },],
  xaxis: {
      categories: ['Jan','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
      axisBorder: {
        show: true,
        color: '#28365f',
      },  
      axisTicks: {
        show: true,
        color: '#28365f',
      },    
  },
  legend: {
    offsetY: -10,
  },
  yaxis: {
      title: {
          text: 'Visitors'
      },      
  },
  fill: {
      opacity: 1

  },
  // legend: {
  //     floating: true
  // },
  grid: {
      row: {
          colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
          opacity: 0.2
      },
      borderColor: '#f1f3fa'
  },
  tooltip: {
    theme: "dark",   
      y: {
          formatter: function (val) {
              return "" + val + "k"
          }
      }
  }
}

var chart = new ApexCharts(
  document.querySelector("#ana_dash_1"),
  options
);

chart.render();


// traffice chart


var optionsCircle = {
    chart: {
      type: 'radialBar',
      height: 240,
      offsetY: -30,
      offsetX: 20,
      dropShadow: {
        enabled: true,
        top: 5,
        left: 0,
        bottom: 0,
        right: 0,
        blur: 5,
        color: '#010f42',
        opacity: 0.35
      },
    },
    plotOptions: {
      radialBar: {
        inverseOrder: true,      
        hollow: {
          margin: 5,
          size: '55%',
          background: 'transparent',
        },
        track: {
          show: true,
          background: '#3c4a82',
          strokeWidth: '10%',
          opacity: 1,
          margin: 5, // margin is in pixels
        },
  
        dataLabels: {
          name: {
              fontSize: '18px',
          },
          value: {
              fontSize: '16px',
              color: '#b9c2d6',
          },          
        }
      },
    },
    series: [71, 63],
    labels: ['Domestic', 'International'],
    legend: {
      show: true,
      position: 'bottom',
      offsetX: -40,
      offsetY: -10,
      formatter: function (val, opts) {
        return val + " - " + opts.w.globals.series[opts.seriesIndex] + '%'
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        type: 'horizontal',
        shadeIntensity: 0.5,
        inverseColors: true,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [0, 100],
        gradientToColors: ["#ffb822", "#5d78ff", "#34bfa3"]
      }
    },
    stroke: {
      lineCap: 'round',
      colors: ["#515988"]
    },
  }
  
  var chartCircle = new ApexCharts(document.querySelector('#circlechart'), optionsCircle);
  chartCircle.render();
  
  
  
  var iteration = 11
  
  function getRandom() {
    var i = iteration;
    return (Math.sin(i / trigoStrength) * (i / trigoStrength) + i / trigoStrength + 1) * (trigoStrength * 2)
  }
  
  function getRangeRandom(yrange) {
    return Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min
  }
  
  window.setInterval(function () {
  
    iteration++;
  
    chartCircle.updateSeries([getRangeRandom({ min: 10, max: 100 }), getRangeRandom({ min: 10, max: 100 })])
  
  
  }, 3000)


// saprkline chart


var dash_spark_1 = {
    
  chart: {
      type: 'area',
      height: 85,
      sparkline: {
          enabled: true
      },
      dropShadow: {
        enabled: true,
        top: 12,
        left: 0,
        bottom: 5,
        right: 0,
        blur: 2,
        color: '#010f42',
        opacity: 0.1
    },
  },
  stroke: {
      curve: 'smooth',
      width: 2
    },
  fill: {
      opacity: 0.05,
  },
  series: [{
    data: [4, 8, 5, 10, 4, 16, 5, 11, 6, 11, 30, 10, 13, 4, 6, 3, 6]
  }],
  yaxis: {
      min: 0
  },
  tooltip: {
    theme: "dark",
  },
  colors: ['#f3c74d'],
}
new ApexCharts(document.querySelector("#dash_spark_1"), dash_spark_1).render();



//Device-widget

if(<?php echo $_smarty_tpl->tpl_vars['user_level_2']->value;?>
 = 'superadmin'){
	var dataActive = <?php echo $_smarty_tpl->tpl_vars['active_users2']->value;?>
;
	var dataInactive = <?php echo $_smarty_tpl->tpl_vars['inactive_users2']->value;?>
;
}else{
	var dataActive = <?php echo $_smarty_tpl->tpl_vars['active_users']->value;?>
;
    var dataInactive = <?php echo $_smarty_tpl->tpl_vars['inactive_users']->value;?>
;
}

var options = {
  chart: {
      height: 250,
      type: 'donut',
      dropShadow: {
        enabled: true,
        top: 10,
        left: 0,
        bottom: 0,
        right: 0,
        blur: 2,
        color: '#010f42',
        opacity: 0.15
    },
  }, 
  plotOptions: {
    pie: {
      donut: {
        size: '80%'
      }
    }
  },
  dataLabels: {
    enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
  },
 
  series: [dataActive, dataInactive],
  legend: {
      show: true,
      position: 'bottom',
      horizontalAlign: 'center',
      verticalAlign: 'middle',
      floating: false,
      fontSize: '14px',
      offsetX: 0,
      offsetY: -13
  },
  labels: [ "Active", "Inactive"],
  colors: ["#5d78ff", "#fd3c97"],
 
  responsive: [{
      breakpoint: 600,
      options: {
        plotOptions: {
            donut: {
              customScale: 0.2
            }
          },        
          chart: {
              height: 240
          },
          legend: {
              show: false
          },
      }
  }],

  tooltip: {
    theme: 'dark',
    y: {
        formatter: function (val) {
            return   val + " Clients"
        }
    }
  }
  
}

var chart = new ApexCharts(
  document.querySelector("#clients"),
  options
);

chart.render();

<?php echo '</script'; ?>
><?php }
}
