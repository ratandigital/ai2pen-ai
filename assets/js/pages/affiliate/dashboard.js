"use strict";

	var monthly_subscriber_gain_chart = document.getElementById("monthly_subscriber_years").getContext("2d");
	var chart_color1 = monthly_subscriber_gain_chart.createLinearGradient(0, 0, 0, 120);
	chart_color1.addColorStop(0, 'rgb(106, 0, 91,.5)');
	chart_color1.addColorStop(1,'rgb(13, 139, 241,.3)');

	var chart_color2 = monthly_subscriber_gain_chart.createLinearGradient(0, 0, 0, 120);
	chart_color2.addColorStop(0, 'rgb(7, 94, 84.5)');
	chart_color2.addColorStop(1,'rgb(37, 211, 102,.3)');
	var monthly_subscriber_gain_chart_bar = new Chart(monthly_subscriber_gain_chart, {
	  data: {
	    labels: affiliate_gain_data_month_names,
	    datasets: [{
	      type: 'line',
	      label: local_subscribers,
	      data: subscriber_gain_data_month_data,
	      borderColor: '#0D8BF1',
	      backgroundColor: chart_color1,
	      pointBackgroundColor: '#0D8BF1',
	      borderWidth:1,
	      pointRadius: 2,
	      pointHoverRadius: 2
	    },
	    {
	      type: 'line',
	      label: total_income,
	      data: affiliate_gain_month_data,
	      borderColor: '#0D8BF1',
	      backgroundColor: chart_color2,
	      pointBackgroundColor: '#0D8BF1',
	      borderWidth:1,
	      pointRadius: 2,
	      pointHoverRadius: 2
	    }]
	  },
	  options: {
	    responsive: true,
	      maintainAspectRatio: true,
	      scales: {
	        yAxes: [{
	          gridLines: {
	            drawBorder: false,
	            display: false
	          },
	          ticks: {
	            beginAtZero: true,
	            fontColor: "#686868"
	          },
	        }],
	        xAxes: [{
	          offset: true,
	          ticks: {
	            beginAtZero: true,
	            fontColor: "#686868",
	            stepSize: step_size
	          },
	          gridLines: {
	            display: false
	          },
	          barPercentage: 0.5
	        }]
	      },
	      legend: {
	        display: false,
	        position: 'bottom'
	      },
	      elements: {
	        point: {
	          radius: 2
	        }
	      }
	}
	});

	var unitlist = ["","K","M","B","T"];
	function num_format(number) {
	    let sign = Math.sign(number);
	    let unit = 0;
	    
	    while(Math.abs(number) >= 1000)
	    {
	      unit = unit + 1; 
	      number = (Math.abs(number) / 1000).toFixed(2);
	    }

	    return sign*Math.abs(number) + unitlist[unit];
	}



  var ecommerce_earning_chart = document.getElementById("ecommerce_earning_chart").getContext("2d");
  var purple_orange_gradient = ecommerce_earning_chart.createLinearGradient(0, 0, 0, 600);
  purple_orange_gradient.addColorStop(0, 'orange');
  purple_orange_gradient.addColorStop(1, 'purple');
  var ecommerce_earning_chart_bar = new Chart(ecommerce_earning_chart, {
    data: {
      labels: earning_chart_labels,
      datasets: [{
        type: 'line',
        label: ecommerce_earning,
        data: earning_chart_values,
        backgroundColor: purple_orange_gradient,
        borderColor:"transparent",
      }]
    },
    options: {
      responsive: true,
        maintainAspectRatio: true,
        scales: {
          yAxes: [{
            gridLines: {
              drawBorder: false,
              display: false
            },
            ticks: {
              beginAtZero: true,  
              fontColor: "#686868"
            },
          }],
          xAxes: [{
            offset: true,
            ticks: {
              beginAtZero: true,
              fontColor: "#686868",
              stepSize: 1
            },
            gridLines: {
              display: false
            },
            barPercentage: .9
          }]
        },
        legend: {
          display: false,
          position: 'bottom'
        },
        elements: {
          point: {
            radius: 2
          }
        }
  }
  });


