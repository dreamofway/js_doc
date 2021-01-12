/**
 * Theme: Adminto Dashboard
 * Author: Coderthemes
 * Chartist chart
 */

//Simple line chart
new Chartist.Line('#simple-line-chart-1', {
  labels: ['8/1', '8/2', '8/3', '8/4', '8/5', '8/6', '8/7', '8/8', '8/9', '8/10'],
  series: [
    [12, 9, 7, 8, 5,12, 9, 7, 8, 5],
    [2, 1, 3.5, 7, 3, 2, 1, 3.5, 7, 3],
    [1, 3, 4, 5, 6, 1, 3, 4, 5, 100]
  ]
}, {
  fullWidth: true,
  chartPadding: {
    right: 40
  },
  plugins: [
    Chartist.plugins.tooltip()
  ]
});

new Chartist.Line('#simple-line-chart-2', {
  labels: ['8/1', '8/2', '8/3', '8/4', '8/5', '8/6', '8/7', '8/8', '8/9', '8/10'],
  series: [
    [12, 9, 7, 8, 5,400, 9, 7, 8, 900],
    [2, 1, 3.5, 300, 3, 2, 1, 3.5, 7, 3],
    [1, 3, 400, 5, 6, 1, 300, 4, 5, 100]
  ]
}, {
  fullWidth: true,
  chartPadding: {
    right: 40
  },
  plugins: [
    Chartist.plugins.tooltip()
  ]
});

new Chartist.Line('#simple-line-chart-3', {
  labels: ['8/1', '8/2', '8/3', '8/4', '8/5', '8/6', '8/7', '8/8', '8/9', '8/10'],
  series: [
    [12, 9, 7, 8, 5,12, 9, 7, 8, 5],
    [2, 1, 3.5, 7, 3, 600, 1, 3.5, 7, 3],
    [1, 3, 4, 500, 6, 1, 3, 4, 5, 100]
  ]
}, {
  fullWidth: true,
  chartPadding: {
    right: 40
  },
  plugins: [
    Chartist.plugins.tooltip()
  ]
});

//Overlapping bars on mobile

var data = {
  labels: ['8/1', '8/2', '8/.', '8/4', '8/5', '8/6', '8/7', '8/8', '8/9', '8/10', '8/11', '8/30'],
  series: [
    [5, 4, 3, 7, 5, 10, 3, 4, 8, 10, 6, 8]
  ]
};

var options = {
  seriesBarDistance: 10
};

var responsiveOptions = [
  ['screen and (max-width: 640px)', {
    seriesBarDistance: 5,
    axisX: {
      labelInterpolationFnc: function (value) {
        return value[0];
      }
    }
  }]
];

new Chartist.Bar('#overlapping-bars', data, options, responsiveOptions);





