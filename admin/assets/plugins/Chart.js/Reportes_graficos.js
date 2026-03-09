
(function(window, document, $, undefined) {
	  "use strict";
	$(function() {

		if ($('#lineChart').length) {
			
			var ctx = document.getElementById('lineChart').getContext('2d');
			var myChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'],
					datasets: [{
						label: 'Fragancia 1',
						data: [13, 20, 4, 18, 7, 4, 8],
						backgroundColor: "rgb(191, 51, 118, 0.5)",
						borderColor: "transparent",
						pointRadius :"0",
						borderWidth: 1
					}, {
						label: 'Fragancia 2',
						data: [3, 30, 6, 6, 3, 4, 11],
						backgroundColor: "rgba(219, 150, 0, 0.5)",
						borderColor: "transparent",
						pointRadius :"0",
						borderWidth: 1
					},{
						label: 'Fragancia 3',
						data: [10, 25, 5, 3, 10, 14, 15],
						backgroundColor: "rgba(74, 114, 178, 0.5)",
						borderColor: "transparent",
						pointRadius :"0",
						borderWidth: 1
					},{
						label: 'Fragancia 4',
						data: [20, 25, 6, 20, 8, 6, 11],
						backgroundColor: "rgba(95, 161, 153, 0.5)",
						borderColor: "transparent",
						pointRadius :"0",
						borderWidth: 1
					}]
				},
			options: {
				legend: {
				  display: true,
				  labels: {
					fontColor: '#ddd',  
					boxWidth:40
				  }
				},
				tooltips: {
				  enabled:false
				},	
			  scales: {
				  xAxes: [{
					ticks: {
						beginAtZero:true,
						fontColor: '#ddd'
					},
					gridLines: {
					  display: true ,
					  color: "rgba(221, 221, 221, 0.08)"
					},
				  }],
				   yAxes: [{
					ticks: {
						beginAtZero:true,
						fontColor: '#ddd'
					},
					gridLines: {
					  display: true ,
					  color: "rgba(221, 221, 221, 0.08)"
					},
				  }]
				 }

			 }
			});
			
		}




		if ($('#pieChart').length) {
			var ctx = document.getElementById("pieChart").getContext('2d');
			var myChart = new Chart(ctx, {
				type: 'pie',
				data: {
					labels: ["Fragancia 1", "Fragancia 2", "Fragancia 3", "Fragancia 4"],
					datasets: [{
						backgroundColor: [
							"rgb(191, 51, 118, 0.7)",
							"rgba(219, 150, 0, 0.7)",
							"rgba(74, 114, 178, 0.7)",
							"rgba(95, 161, 153, 0.7)"
						],
						data: [35, 25, 20, 20],
						borderWidth: [0, 0, 0, 0]
					}]
				},
			options: {
			   legend: {
				 position :"right",	
				 display: true,
				    labels: {
					  fontColor: '#ddd',  
					  boxWidth:15
				   }
				}
			   }
			});
		}


	});

})(window, document, window.jQuery);