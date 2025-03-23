import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { NgChartsModule, BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration, ChartType } from 'chart.js';
import { StockPriceService } from '../../stock-price.service';
@Component({
  selector: 'app-participant-line',
  standalone: true,
  imports: [CommonModule, FormsModule,  NgChartsModule],
  templateUrl: './participant-line.component.html',
  styleUrl: './participant-line.component.css'
})
export class ParticipantLineComponent    implements OnInit {

  data: any;
  message: any;
  historicalData: any;
  sortedDates: any;
  @Input() symbol: any = 1;
  @Input() id: any = 1;
  @Input() id2: any = 1;
  @Input() end_time: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  public barChartType: ChartType = 'bar';

  apiData: any;
  daily: any;

// We'll create arrays for labels and each dataset
  labels: any = [];
  freeData: number[] = [];
  resData: number[] = [];
  taFreeData: number[] = [];
  totalData: number[] = [];
  chartPriceData: any;
  chartVolumeData: any;
  chartDateData: any;
  highestPrice: number = 1;

  @ViewChild('myChart', { static: false, read: BaseChartDirective })
  chart: BaseChartDirective | undefined;

public updateChart() {
  this.chart?.update();
}

  constructor(
    private _dataService: DataService,
) { }


public XlineChartOptions: ChartConfiguration<'line'>['options'] = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      grid: {
        color: 'rgba(0, 0, 0, 0.3)', 
        lineWidth: 1,
      },
      ticks: {
        autoSkip: true,
        maxTicksLimit: 10
      }
    },
    y: {
      grid: {
        color: 'rgba(0, 0, 0, 0.3)', // match or choose a darker/lighter color
        lineWidth: 1,               // increase thickness if you like
      }
      // You can also set ticks, etc., here if needed
    }
  }
};

public XlineChartData: ChartConfiguration<'line'>['data'] = {
  labels: [], // X-axis labels (date or category strings)
  datasets: [
    {
      label: 'Free',
      data: [], 
      borderColor: 'rgba(255, 0, 0, 1)', // Solid Red
      borderWidth: 3,                    // Thicker line for visibility
      fill: false,                       // no fill area under the line
      tension: 0,                        // straight lines (optional)
    },
/*     {
      label: 'TA Free',
      data: [],
      fill: false,
    }, 
   {
      label: 'TA Restricted',
      data: [],
      fill: false,
    },
    {
      label: 'Total',
      data: [],
      fill: false,
    } */
   ]
};

public lineChartData: ChartConfiguration<'line'>['data'] = {
  labels: [],
  datasets: [
    {
      label: 'Total Shares',
      data: [], // Flat line to test transparency
      borderColor: 'rgba(0, 255, 0, 1)', // Solid red
      backgroundColor: 'rgba(0, 255, 0, 0.2)', // Light red fill
      borderWidth: 2,
      tension: 0, // Straight line
      fill: false, // No background
      pointRadius: 1, // Show points
      pointBackgroundColor: 'green',
      pointBorderColor: 'green'
    },
    {
      label: 'NOBO',
      data: [], // Flat line to test transparency
      borderColor: 'rgba(255, 0, 0, 1)', // Solid red
      backgroundColor: 'rgba(255, 0, 0, 0.2)', // Light red fill
      borderWidth: 2,
      tension: 0, // Straight line
      fill: false, // No background
      pointRadius: 1, // Show points
      pointBackgroundColor: 'red',
      pointBorderColor: 'red'
    },
  ]
};

public lineChartOptions: ChartConfiguration<'line'>['options'] = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      grid: { color: 'rgba(0, 0, 0, 0.1)' }, // Lighter x-axis grid
      ticks: {
        autoSkip: true,
        maxTicksLimit: 10
      }
    },
    y: {
      grid: { color: 'rgba(0, 0, 0, 0.1)' } // Lighter y-axis grid
    }
  },
};


formatYYYYMMDDtoMMDD(dateStr: string): string {
  const [year, month, day] = dateStr.split('-');
  return `${month}/${day}/${year}`; // e.g., "03/07"
}

  ngOnInit(): void
  {      
    this._dataService.getData("get-participant-graph", this.id, this.id2, this.symbol).subscribe((data: any)=> { 

      //        $output['labels']=$labels;
      //        $output['free']=$free;
      //        $output['restricted']=$ta_res;
      //        $output['total']=$total;

      this.data=data;
      this.labels=data.labels;
//      this.freeData=data.free;
//      this.resData=data.restricted;
      this.totalData=data.total;
      this.resData=data.nobo;
//      this.taFreeData=data.ta_free;

      this.lineChartData.labels = [ ...this.labels ];
 //     this.lineChartData.datasets[0].data = [ ...this.freeData ];
      this.lineChartData.datasets[1].data = [ ...this.resData ];
 //     this.lineChartData.datasets[2].data = [ ...this.taFreeData ];
      this.lineChartData.datasets[0].data = [ ...this.totalData ];
      this.updateChart();
  });
    }
}
