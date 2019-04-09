/**
 * PROJECT TITLE:: LEARNERS ANALYTICS DASHBOARD
 * DEVELOPER:: AKINWALE ADETOLA <hackinwale.developer@gmail.com>
 * DATE:: MARCH 21, 2019
 * 
 * ___________
 * DESCRIPTION
 * ___________
 * THIS FILE HAS ALL THE CODE FOR THE WHOLE 12 CHARTS ON THE LEARNERS DASHBOARD IN ORDER.
 * 
 */


//Call Learner Dashboard here
// learnersDashboard();

/**
 * TASK 1:: IMPLEMENTATION OF RING CHARTS
 */
function ringDataProcessor(divId, color){
  //create a sample data
  var sampleData = {
    totalCourse: 10,
    takenCourse: 6,
    outstandingCourse: 4
  };
  var ring = drawRingChart().data(sampleData)
                            .divId(divId)
                            .color(color);

  d3.select(divId).call(ring);

}//end processor
function drawRingChart(){
  //updatables
  let data;
  let divId;  
  let color;

  function drawRing(selection){
    selection.each(function(){

      let containerWidth = parseInt(($(divId).parent().css('width')))
      let svgWidth = containerWidth-20, svgHeight = containerWidth/2,
          margin = {top: 10, bottom: 10, right: 10, left: 10},
          width = svgWidth - margin.left, height = svgHeight - margin.top;

      let outerRadius = width/4,
          innerRadius = outerRadius - margin.top - margin.bottom;
      let endAngle = 360 * Math.PI/180;

      var containerDiv = d3.select(this),
          svg = containerDiv.append('svg')
                  .attr('width', width)
                  .attr('height', height)
      //create the arc
      var arc = d3.arc()
                        .innerRadius(innerRadius)
                        .outerRadius(outerRadius)
                        .startAngle(0)
                        // .endAngle(endAngle)
      //create arc group
      arcGroup = svg.append('g')
                  .attr('transform', 'translate('+ width/2 +','+ height/2 +')')

      //Set the path for the arc
      arcPath = arcGroup.append('path')
                  .attr('class', 'path'+divId)
                  .datum({endAngle: endAngle})
                  .style('fill', color)
                  .style('opacity', 0.75)
                  .attr('d', arc)

      //Position figure at the center of the arc
      svg.append('text')
                  .data([data])
                  .text(function(d,i){
                    //use case to choose the value for rings
                    switch (divId){
                      case '#total-course':
                        return d.totalCourse;
                      case '#course-taken':
                        return d.takenCourse;
                      default:
                        return d.outstandingCourse;
                    }
                  })
                  .style('fill', color)
                  .style('font-size', '2em')
                  .style('font-weight', 'bold')
                  .attr('text-anchor', 'middle')
                  .attr('transform', 'translate('+ width/2 +','+ height/2 +')')         
    });

  }
  drawRing.data = function(value){
    if(!arguments.length)
      return drawRing;
    data = value;
    return drawRing;
  }

  drawRing.divId = function(value){
    if(!arguments.length)
      return drawRing;
    divId = value;
    return drawRing
  }

  drawRing.color = function(value){
    if(!arguments.length)
      return drawRing;
    color = value;
    return drawRing;
  }
  return drawRing;
}//end ringchart

/**
 * TASK 2:: IMPLEMENTATION OF PIE CHART
 */
function pieDataProcessor(divId){
  //Data required are Course names and Course grades
  var courseAndGrades = [{ //In json format
      courseName: 'P5', courseGrade: 'A+'
    },
    {
      courseName: 'Engexam1', courseGrade: 'A'
    },
    {
      courseName: 'P2', courseGrade: 'B'
    },
    {
      courseName: 'P3', courseGrade: 'C'
    },
    {
      courseName: 'P4', courseGrade: 'D'
    }];

  var gradesCount = [ { grade: 'A', count: 2 },
                      { grade: 'B', count: 4},
                      { grade: 'C', count: 2},
                      { grade: 'D', count: 3},
                      { grade: 'E', count: 1},
                      { grade: 'F', count: 1}]
    //instantiate the chart
    var pieChart = drawPieChart().data(gradesCount)
                                  .grades(gradesCount)
                                  .divId(divId);
    //initiate the chart
    d3.select(divId).call(pieChart);

}//end processor
function drawPieChart(){
  //updatables
  var data;
  var grades;
  var divId;

  function drawPie(selection){
    selection.each(function(){
      let containerWidth = parseInt(($(divId).parent().css('width')))
      let svgWidth = containerWidth-20, svgHeight = containerWidth-20,
          margin = {top: 10, bottom: 10, right: 10, left: 10},
          width = svgWidth - margin.left, height = svgHeight - margin.top;

      const color = d3.scaleOrdinal(["#66c2a5","#fc8d62","#8da0cb",
        "#e78ac3","#a6d854","#ffd92f"]);
      let radius = height/2.5;
      //set up the svg
      var containerDiv = d3.select(this);
      var svg = containerDiv.append('svg')
                          .attr('width', svgWidth)
                          .attr('height', svgHeight)
                          .attr('class', 'piechart');
      //Set a grouping container
      var gPie = svg.append('g')
                          .attr('transform', 'translate('+ (width-50)/2 +','+ height/2 +')')
                          
      //create the pie-s layout
      var pie = d3.pie()
                  .sort(null)
                  .value(function(d){ return d.count; })(grades);
      logger(pie)
      //define the arc around the pie-s
      let arc = d3.arc()
                  .innerRadius(0)
                  .outerRadius(radius)
                  .padAngle(.05)
                  .padRadius(50)

      //call the pie
      let arcPath = gPie.selectAll('.arc-path')
                .data(pie).enter().append('path')
                  .attr('class', 'arc-path')
                  .style('fill', function(d){ return color(d.index) })
                  .style('stroke', 'white')
                  .style('stroke-width', '3px')
                  .attr('d', arc);
      //Set the text for the pie
      let pieText = gPie.selectAll('.pie-text')
                  .data(pie).enter().append('text')
                  .attr('class', 'pie-text')
                  .each(function(d){
                    let center = arc.centroid(d);
                    d3.select(this)
                      .attr('x', center[0])
                      .attr('y', center[1])
                      .attr('text-anchor', 'middle')
                      .style('font-size', '1.5em')
                      .style('font-weight', 'bold')
                      .style('fill', 'white')
                      .text(d.data.count);
                  })
                  .on('mouseover', function(){
                    //change the text to course
                    d3.select(this)
                        .text('Course Here')
                        .style('font-size', '.5em')
                  })
                  .on('mouseout', function(d){
                    d3.select(this)
                        .text(d.data.count)
                        .style('font-size', '1.5em');
                  });
      //Show legend for the pie
      let gLegends = svg.append('g')
                        .attr('transform', 'translate('+ width*0.9 +','+ margin.top +')')
                        .attr('class', 'legends')
                        .selectAll('.legend')
                      .data(pie);
      let legend = gLegends.enter().append('g')
                        .attr('transform', function(d,i){
                          return 'translate('+ 0 +','+ (i+1)*20 +')'
                        });
      let legendRect = legend.append('rect')
                          .attr('width', 20)
                          .attr('height', 20)
                          .style('fill', function(d,i){ return color(d.index)})
                          .style('stroke', 'white');
      let legendText = legend.append('text')
                          .text(function(d,i){ return d.data.grade})
                          .attr('x', 20)
                          .attr('y', 15)
                          .style('fill', function(d){ return color(d.index)})
                          .style('font-weight', 'bold')
    });
  }//end drawPie

  drawPie.data = function(value){
    if(!arguments.length)
      return drawPie;
    data = value;
    return drawPie;
  }
  drawPie.grades = function(value){
    if(!arguments.length)
      return drawPie;
    grades = value;
    return drawPie;
  }
  drawPie.divId = function(value){
    if(!arguments.length)
      return drawPie;
    divId = value;
    return drawPie;
  }
  return drawPie;
}//end drawPieChart

/**
 * TASK 3:: IMPLEMENTATION OF BAR CHART
 */
function barDataProcessor(divId){
  //Sample data with courses and score
  var courseAndScore = [{ //In json format
    courseName: 'P5', score: 73
  },
  {
    courseName: 'Engexam1', score: 92
  },
  {
    courseName: 'P2', score: 67
  },
  {
    courseName: 'P3', score: 80
  },
  {
    courseName: 'P4', score: 75
  }];

  //Instantiate and init chart
  var barChart = drawBarChart().data(courseAndScore)
                                .divId(divId);

  d3.select(divId).call(barChart);

}//end processor
function drawBarChart(){
  //updatables
  let data;
  let divId;

  const FORMAT_PERCENTAGE = d3.format('.0%');

  function drawBar(selection){
    selection.each(function(){

      let containerWidth = parseInt(($(divId).parent().css('width')))
      let svgWidth = containerWidth, svgHeight = containerWidth*.65,
          margin = {top: 10, bottom: 50, right: 10, left: 50},
          width = svgWidth - margin.left, height = svgHeight - margin.bottom;

      let color = d3.scaleOrdinal(d3.schemeCategory10)
      var chart = d3.select(this)
            .append("svg")
              .attr("width", svgWidth)
              .attr("height", svgHeight)
              .attr('class', 'chart')            
      var gChart = chart.append("g")
              .attr('class', 'chart-container')
              .attr("transform", 
                  "translate(" + margin.left + "," + margin.top + ")");
      //set scale and domain
      var x = d3.scaleBand().rangeRound([0, width], 0.05);
      var y = d3.scaleLinear().range([height, 0]);
      x.domain(data.map(function(d) { return d.courseName; }));
      y.domain([0, 100]);
      //set up the X-Y axis
      var gAxis = gChart.append('g')
              .attr('class', 'axes')
      var xAxis = gAxis.append("g")
              .attr("class", "x-axis")
              .attr("transform", "translate(0," + height + ")")
              .call(d3.axisBottom(x))
              .style('font-size', '.9em')
              // xAxis.append('text')
              //     .attr('class', 'xtitle')
              //     .attr('fill', 'grey')
              //     .text('Genres')
              //     .attr("transform", "translate(100,"+ height +")")
          xAxis.selectAll("text")
              .style("text-anchor", "middle")
              .style('font-weight', 'bold')
              // .style("fill", function(d){ return color(d.courseName)})
      var yAxis = gAxis.append("g")
              .attr("class", "y-axis")
              .call(d3.axisLeft(y).tickFormat(function(d){ return FORMAT_PERCENTAGE(d/100)}))              
              .style('font-size', '.9em')
          // yAxis.append("text")
          //     .attr('class', 'ytitle')
          //     // .attr("transform", "rotate(-90)")
          //     .attr("y", 6)
          //     .attr("dy", "-.71em")
          //     .attr('dx', '-1.5em')
          //     .style('fill', 'grey')
          //     .text(dataPointSelected)

      //setup the bar with rect
      var gBars = gChart.append('g')
              .attr('class', 'bars')
      var bar = gBars.selectAll('.bar').data(data, function(d){ return d})
      
      var rectBar = bar.enter().append('g')
              .attr('class', 'bar')
              // .merge(bar)
              // .attr('transform', function(d,i){ return 'translate('+ i * x.bandwidth() +', 0)'})
          rectBar.append("rect")
              .attr('class', 'rect-bar')
          //   .merge(bar)
              .style("fill", function(d){ return color(d.courseName)})
              .style('opacity', .75)
              .attr("x", function(d) { return x(d.courseName); })
              .attr('y', height)
              .transition().duration(2000).ease(d3.easeExp)
              .attr("y", function(d) { return y(d.score); })
              .attr("width", x.bandwidth() - 5)
              .attr("height", function(d) { return height - y(d.score); })
      // .on('mouseover',function(d){ logger(d.score) });
          rectBar.append('text')
              .attr('class', 'bar-label')
          //   .merge(bar)
              .attr('transform', function(d){ return (' translate('+ x(d.courseName) +','+ (y(d.score) ) +') rotate(-90)')})
              // .attr('x', function(d){return x(d.courseName)})
              // .attr('y', function(d){ return y(d.score)+5})
              .transition().duration(2000).ease(d3.easeExp)
              .style("fill", function(d){ return color(d.courseName)})
              .style('font-size', '12px')
              .style('writing-mode', 'tb')
              .attr('text-anchor', 'middle')
              .attr('dy', '1.8em')
              .attr('dx', '.55em')
              .text(function(d){ return (d.score)})
              // .attr('transform', function(d){ return 'rotate(90) translate('+ y(d.score) +','+ x(d.courseName) +')'})            
          
          logger('Bar chart should be on the screen now')

          var t = d3.transition()
                      .duration(500)
                      .ease(d3.easeBackIn);

    });    
  }//end drawBar

  drawBar.data = function(value){
    if(!arguments.length)
      return drawBar;
    data = value;
    return drawBar;
  }
  drawBar.divId = function(value){
    if(!arguments.length)
      return drawBar;
    divId = value;
    return drawBar;
  }

  return drawBar;
}//end drawBarChart

/**
  * TASK 4:: IMPLEMENTATION OF LINE CHART
  */
 function lineDataProcessor(divId){
  //Data needed will be Session and Average performance for the session
  let sessionPerformance = [
            {session: 'Kinder', performance: 95},
            {session: 'Pry 1', performance: 82},
            {session: 'Pry 2', performance: 75},
            {session: 'Pry 3', performance: 90},
            {session: 'Pry 4', performance: 80},
            {session: 'Pry 5', performance: 77}
  ];

  var lineChart = drawLineChart().data(sessionPerformance).divId(divId);

  d3.select(divId).call(lineChart);

 }//end processor
 function drawLineChart(){
   //updatables
   let data;
   let divId;

    const parseTime = d3.timeParse('%x')

    function drawLine(selection){
        selection.each(function(){

          let containerWidth = parseInt(($(divId).parent().css('width')))
          let svgWidth = containerWidth, svgHeight = containerWidth*.65,
              margin = {top: 10, bottom: 50, right: 10, left: 50},
              width = svgWidth - margin.left, height = svgHeight - margin.bottom;            

            var containerDiv = d3.select(this)
            var svg = containerDiv.append('svg')
                            .attr('width', svgWidth)
                            .attr('height', svgHeight)
            
            // Place a master container in svg and format
            var chart = svg.append('g')
                            .attr('transform', 'translate('+ margin.left +',' +margin.top+')')
                            .attr('class', 'container')
                            .style('fill', 'green')
                // chart.append('text')
                //             .attr('x', margin.left)
                //             .text('Expenses made this month')
            // Define custom tooltip
            var tooltip = d3.select(divId)
                          .append('div')
                            .attr('class', 'tooltip')
            //setting the range and scale            
            var x = d3.scaleBand().rangeRound([0, width], 0.05)
                            .domain(data.map(function(d) { return d.session; }));
            // var x = d3.scaleTime()
            //                 .domain(d3.extent(data, function(d){ return d.session}))
            //                 .rangeRound([0, width])
            var y = d3.scaleLinear()
                            .domain([50, d3.max(data, function(d){ return d.performance})])
                            .rangeRound([height, 0])
            //set the x-y axis
            var xAxis = chart.append('g')
                            .attr('class', 'x-axis')
                            .attr('transform', 'translate(0,'+ height +')')
                            .call(d3.axisBottom(x)
                                .ticks(5))
            var yAxis = chart.append('g')
                                .attr('class', 'x-axis')
                              .call(d3.axisLeft(y)
                                .ticks(5)
                                .tickFormat(function(d){ return d}))
            // Drawing gridlines
            // var yGrid = chart.append('g')
            //                     .attr('class', 'grid')
            //                   .call(d3.axisLeft(y)
            //                     .ticks(5)
            //                     .tickSize(-width)
            //                     .tickFormat(''))

            // Define the color gradient for the line chart
            var areaColor = chart.append('defs')
                          .append('linearGradient')
                            .attr('id', 'line-gradient')
                            .attr("gradientUnits", "userSpaceOnUse")
                            .attr('x1', 0).attr('x2', 0)
                            .attr('y1', y(90))
                            .selectAll('stop')
                          .data([
                                {offset: '0%', color: 'aqua'},
                                {offset: '100%', color: 'red'}
                            ])
                areaColor.enter().append('stop')
                            .attr('offset', function(d){ return d.offset})
                            .attr('stop-color', function(d){ return d.color})

            // Define the line tool first
            var line = d3.line()
                            .curve(d3.curveMonotoneX)
                            .x(function(d){ return x(d.session)})
                            .y(function(d){ return y(d.performance)})
            // Now define the path
            var linePath = chart.append('path')
                            .attr('class', 'line-path')
                          .datum(data)  
                            // .on('mouseover', function(d){
                            //     dotEnter.attr('r', 4)
                            // }) 
                            // .on('mouseout', function(d){
                            //     dotEnter.attr('r', 0)
                            // })
                            .attr('fill', 'none') 
                            .attr('stroke', 'skyblue')
                            .attr('stroke-width', 2)                      
                            .attr('d', line)
            // Let's define the area too
            var area = d3.area()
                            .curve(d3.curveMonotoneX)
                            .x(function(d){ return x(d.session)})
                            .y0(height)
                            .y1(function(d){ return y(d.performance)})
            // Set path for the area
            var areaPath = chart.append('path')
                            .attr('class', 'area-path')
                          .datum(data)
                            .style('fill', 'skyblue')
                            .style('opacity', .3)
                            .attr('d', area)
            
            // Draw circle to show score
            var dataCircle = chart.selectAll('.dot')
                          .data(data)
            var dotEnter = dataCircle.enter()
                          .append('circle')
                            .attr('class', 'dot')
                            .attr('cx', function(d){ return x(d.session)})
                            .attr('cy', function(d){ return y(d.performance)})
                            .attr('r', 4)
                            // .attr('visibility', 'hidden')
                            .on('mouseover', function(d,i){
                                /* Do some magik here */
                                d3.select(this)
                                    .attr('r', 4)
                                    // .attr('visibility', 'visible')
                                    .style('stroke', 'green')
                                    .style('fill', 'white')
                                    showToolTip(d)
                            })
                            .on('mousemove', moveToolTip)
                            .on('mouseout', function(d){
                                d3.select(this)
                                    .attr('r', 4)
                                    .style('stroke', 'aqua')
                                    hideToolTip()
                            })
                            .attr('fill', 'white')
                            .style('stroke', 'aqua')
                            .style('stroke-width', 2)
                            
            // Create custom tooltip 
            function showToolTip(d) {
                logger('== Tooltip should show now ==')
                const formatDay = d3.timeFormat('%a')
                const formatDate = d3.timeFormat('%B %d')
                const html = `
                    <div><span class="header"> </span> PERFORMANCE NODE</div>
                    <div><span class="header">Performance: </span>${d.performance}</div>
                    <div><span class="header">Session: </span> ${d.session}</div>
                `;
                tooltip.html(html);
                // tooltip.text('We are here')
                tooltip.box = tooltip.node().getBoundingClientRect();
                tooltip.transition().style("opacity", 1);
            
            } // end showToolTip
            //set coordinates for when tooltip move
            function moveToolTip() {
                const top = d3.event.clientY - tooltip.box.height - 5;
                let left = d3.event.clientX - tooltip.box.width / 2;
                if (left < 0) {
                  left = 0;
                } else if (left + tooltip.box.width > window.innerWidth) {
                  left = window.innerWidth - tooltip.box.width;
                }
                tooltip.style("left", left + "px").style("top", top + "px");
              }
            function hideToolTip() {
                tooltip.transition().style("opacity", 0);
              }
        }); //end selection

    }// end drawLine

    drawLine.data = function(value){
            if(!arguments.length){
                return drawLine;
            }
            data = value;
            return drawLine;
        }
    drawLine.divId = function(value){
      if(!arguments.length)
        return drawLine;
      divId = value;
      return drawLine;
    }

    // return the chart
    return drawLine;

}//end drawLines


/**
  * TASK 5:: IMPLEMENTATION OF DONUT CHART
  */
 function donutDataProcessor(divId){
    //The data combination will be the courses and their status
    let data = [
      {course: 'P5', status: 'running'},
      {course: 'Engexam1', status: 'running'},
      {course: 'P2', status: 'completed'},
      {course: 'P3', status: 'completed'},
      {course: 'P4', status: 'expired'}
    ];
    let portionData = [
      {status: 'running', proportion: 50},
      {status: 'completed', proportion: 50},
      {status: 'expired', proportion: 50}
    ];

    //instantiate chart 
    var donutChart = drawDonutChart().data(data)
                                      .portionData(portionData)
                                      .divId(divId);

    d3.select(divId).call(donutChart);

 }//end processor
 function drawDonutChart(){
  //updatables
  let data; //main data from database
  let portionData; //defined for special use
  let divId; //container id

  function drawDonut(selection){
    selection.each(function(){
      // generate drawDonut
      let containerWidth = parseInt(($(divId).parent().css('width')))
      let svgWidth = containerWidth, svgHeight = containerWidth*.65,
          margin = {top: 10, bottom: 10, right: 10, left: 10},
          width = svgWidth - margin.left, height = svgHeight - margin.bottom;            

      var containerDiv = d3.select(this)
      var svg = containerDiv.append('svg')
                      .attr('width', svgWidth)
                      .attr('height', svgHeight)
                    .append('g')
                      .attr('transform', 'translate(' + width / 2 + ',' + height / 2 + ')');

      let colour = d3.scaleOrdinal(d3.schemeCategory10);
      const cornerRadius = 3;
      const padAngle = 0.015;
      const radius = Math.min(width, height) / 2;
      // creates a new pie generator
      var pie = d3.pie()
          .value(function(d) { return d.proportion })
          .sort(null);
      //create the donut arc
      var arc = d3.arc()
          .outerRadius(radius * 0.8)
          .innerRadius(radius * 0.6)
          .cornerRadius(cornerRadius)
          .padAngle(padAngle);

      // this arc is used for aligning the text labels
      var outerArc = d3.arc()
          .outerRadius(radius * 0.9)
          .innerRadius(radius * 0.9);

      // g elements to keep elements within svg modular
      svg.append('g').attr('class', 'slices');
      svg.append('g').attr('class', 'labelName');
      svg.append('g').attr('class', 'lines');

      // add and colour the donut slices
      var path = svg.select('.slices')
          .datum(portionData).selectAll('path')
          .data(pie)
        .enter().append('path')
          .attr('fill', function(d) { return colour(d.data['status']); })
          .attr('d', arc)
          

      // add text labels
      var label = svg.select('.labelName').selectAll('text')
          .data(pie(portionData))
        .enter().append('text')
          .attr('dy', '.35em')
          .html(function(d) {
            logger(d.data);
              // add "key: value" for given category. Number inside tspan is bolded in stylesheet.
              return d.data['status'];// + ': <tspan>' + d.data['proportion'] + '</tspan>';
          })
          .attr('transform', function(d) {
              //calculate the center of the slice
              var pos = outerArc.centroid(d);

              // changes the point to be on left or right depending on where label is.
              pos[0] = radius * 0.95 * (midAngle(d) < Math.PI ? 1 : -1);
              return 'translate(' + pos + ')';
          })
          .style('text-anchor', function(d) {
              // if slice centre is on the left, anchor text to start, otherwise anchor to end
              return (midAngle(d)) < Math.PI ? 'start' : 'end';
          });

      // add lines connecting labels to slice. A polyline creates straight lines connecting several points
      var polyline = svg.select('.lines')
          .selectAll('polyline')
          .data(pie(portionData))
        .enter().append('polyline')
          .attr('points', function(d) {

              // see label transform function for explanations of these three lines.
              var pos = outerArc.centroid(d);
              pos[0] = radius * 0.95 * (midAngle(d) < Math.PI ? 1 : -1);
              return [arc.centroid(d), outerArc.centroid(d), pos]
          })
          .attr('fill', 'none')
          .attr('stroke', function(d){ return colour(d.data['status']); });
      // add tooltip to mouse events on slices and labels
      d3.selectAll('.labelName text, .slices path').call(toolTip);
      

      /** Work extra functions here */ 

      // calculates the angle for the middle of a slice
      function midAngle(d) { return d.startAngle + (d.endAngle - d.startAngle) / 2; }

      // function that creates and adds the tool tip to a selected element
      function toolTip(selection) {

          // add tooltip (svg circle element) when mouse enters label or slice
          selection.on('mouseenter', function (d) {

              svg.append('text')
                  .attr('class', 'toolCircle')
                  .attr('dy', -15) // hard-coded. can adjust this to adjust text vertical alignment in tooltip
                  .html(toolTipHTML(d)) // add text to the circle.
                  .style('font-size', '.9em')
                  .style('text-anchor', 'middle'); // centres text in tooltip

              svg.append('circle')
                  .attr('class', 'toolCircle')
                  .attr('r', radius * 0.55) // radius of tooltip circle
                  .style('fill', colour(d.data['status'])) // colour based on category mouse is over
                  .style('fill-opacity', 0.35);

          });

          // remove the tooltip when mouse leaves the slice/label
          selection.on('mouseout', function () {
              d3.selectAll('.toolCircle').remove();
          });
      }

      // function to create the HTML string for the tool tip. Loops through each key in data object
      // and returns the html string key: value
      function toolTipHTML(d) {

        var tip = '';

            //nest the incoming data from the db
            var result = d3.nest()
                      .key(function(d){ return d['status'] })
                      .entries(data);
            //retrieve the status from the selected arc
            let selectedStatus = d.data['status'];
            let title, courseName ='';
            //now use that selectedstatus to query the result
            for(let j = 0; j<result.length; j++){

              if(result[j].key == selectedStatus){
                  
                let temp = result[j].values;
                title = '<tspan x="0">' + temp.length + ' courses ' + selectedStatus + '</tspan>';
                
                for(let k = 0; k<temp.length; k++){          
                  courseName += '<tspan x="0" dy="1.2em">' + temp[k].course + '</tspan>';
                }//end inner for
              }
              
            }//end outter for
            tip = title +'\n\n\n'+ courseName;
   
          return tip;
      }//end tooltip

      //Let's play with some animation (not applied yet)
      function arcTween() {
        return function(d) {
          // interpolate both its starting and ending angles
          var interpolateStart = d3.interpolate(d.start.startAngle, d.end.startAngle);
          var interpolateEnd = d3.interpolate(d.start.endAngle, d.end.endAngle);
          return function(t) {
            return arc({
              startAngle: interpolateStart(t),
              endAngle: interpolateEnd(t),
            });
          };
        };
      }

    });
    }//end drawDonut

    // getter and setter functions. See Mike Bostocks post "Towards Reusable drawDonuts" for a tutorial on how this works.
    drawDonut.data = function(value) {
        if (!arguments.length) return data;
        data = value;
        return drawDonut;
    }

    drawDonut.divId = function(value) {
        if (!arguments.length) return divId;
        divId = value;
        return drawDonut;
    }

    drawDonut.portionData = function(value) {
        if (!arguments.length) return portionData;
        portionData = value;
        return drawDonut;
    }

  return drawDonut;
 }//end donutChart
 
/**
  * TASK 5:: IMPLEMENTATION OF DONUT CHART
  */
 
  
/**
 * ALL CHARTS POWERHOUSE: THIS TRIGGERS ALL CHARTS WHEN PAGE LOADS
 */
function learnersDashboard(chartType,generatedDivId){
  //I better use switch case here to select which chart to display at a time of call
  switch(chartType){
    case 'RingChart':
      ringDataProcessor('#'+generatedDivId, 'green');
      ringDataProcessor('#'+generatedDivId, 'orange');
      ringDataProcessor('#'+generatedDivId, 'red');
      break;
    case 'PieChart':
      pieDataProcessor('#'+generatedDivId);
      break;
    case 'BarChart':
      barDataProcessor('#'+generatedDivId);
      break;
    case 'LineChart':
      lineDataProcessor('#'+generatedDivId);
      break;
    case 'DonutChart':
      donutDataProcessor('#'+generatedDivId);
      break;

  }//end switch

}//end learnersDashboard

//create logger function
var debugMode = true;
function logger(param){
  if(debugMode)
    console.log(param);
}