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
  selector: 'app-price14days',
  standalone: true,
  imports: [CommonModule, FormsModule,  NgChartsModule],
  templateUrl: './price14days.component.html',
  styleUrl: './price14days.component.css'
})
export class Price14daysComponent  implements OnInit {

  data: any;
  message: any;
  historicalData: any;
  sortedDates: any;
  @Input() symbol: any = 'KSEZ';
  @Input() start_time: any = '';
  @Input() end_time: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  public barChartType: ChartType = 'bar';

  apiData: any;
  daily: any;

// We'll create arrays for labels and each dataset
  labels: any = [];
  priceData: number[] = [];
  volumeData: number[] = [];
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
    private stockPriceService: StockPriceService,
) { }

barChartOptions: ChartConfiguration<'bar'>['options'] = {
  scales: {
    y: {
//      max: this.highestPrice,
      ticks: {
        callback: (value: number | string) => {
          const num = typeof value === 'string' ? parseFloat(value) : value;
          return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            // Force exactly two decimal places:
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }).format(num);
        }
      }
    }
  }
};

// Only one dataset for Price
public barChartData: ChartConfiguration<'bar'>['data'] = {
  labels: this.labels, // will fill in at runtime
  datasets: [
    {
      label: 'Price',
      data: this.priceData,  
      backgroundColor: 'DodgerBlue'
    }
  ]
};


  ngOnInit(): void
  {      
      let uid=localStorage.getItem('uid');
      this.stockPriceService.getCurrentPrice(this.symbol).subscribe({
        next: (data) => {
          console.log(data);
        },
        error: (error) => console.error('Error fetching data:', error),
      });
      this.stockPriceService.getDailyHistorical(this.symbol).subscribe({
        next: (data) => {
          const timeSeries = data['Time Series (Daily)'] || {};
          this.apiData = data;
          this.historicalData = this.transformData(timeSeries);
          this.sortedDates = Object.keys(this.historicalData).reverse();
          
          // Grab the last 14 days
          const last14 = this.sortedDates.slice(-14);
      
          for (const date of this.sortedDates) {
            const dayInfo = this.historicalData[date];
            const closePrice = parseFloat(dayInfo["close"]);
            let vol = parseFloat(dayInfo["volume"]);
      
            // Correct the typo here
            this.priceData.push(closePrice);
            if (closePrice>this.highestPrice) this.highestPrice = closePrice;
      
            if (Number.isNaN(vol)) { vol = 0; }
            this.volumeData.push(vol);
      
            this.labels.push(this.formatYYYYMMDDtoMMDD(dayInfo['date']));
          }
      
          // Reassign arrays to ensure Angular sees new references
          console.log('Final labels:', this.labels);
          console.log('Final priceData:', this.priceData);
          console.log(this.highestPrice)

          this.barChartData.labels = [ ...this.labels.slice(-14) ];
          this.barChartData.datasets[0].data = [ ...this.priceData.slice(-14) ];
          if (this.barChartOptions!.scales!['y']) {
            this.barChartOptions!.scales!['y'].max = this.highestPrice * 1.1;
          }
      
          // Force chart update
          this.chart?.update();
        },
        error: (error) => console.error('Error fetching data:', error),
      });
  }

  formatYYYYMMDDtoMMDD(dateStr: string): string {
    // dateStr is "YYYY-MM-DD"
    const [year, month, day] = dateStr.split('-');
    return `${month}/${day}`; // e.g., "03/07"
  }

  transformData(timeSeries: any): any[] {
    return Object.keys(timeSeries).map((date) => {
      return {
        date: date,
        open: +timeSeries[date]['1. open'],
        high: +timeSeries[date]['2. high'],
        low: +timeSeries[date]['3. low'],
        close: +timeSeries[date]['4. close'],
        volume: +timeSeries[date]['6. volume'],
      };
    });
  }

  getLast5Days(data: any[]): any[] {
    // Sort descending by date, then slice top 5
    return data.sort((a, b) => {
      return new Date(b.date).getTime() - new Date(a.date).getTime();
    }).slice(0, 5);
  }

// FS34H3MQECXBP7UH

}
