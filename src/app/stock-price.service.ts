import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class StockPriceService {
  private apiKey = 'TFQ5M15KX1E1QKUT'; // Insert your real key here
  private baseUrl = 'https://www.alphavantage.co/query';

constructor(private http: HttpClient) {}

getDailyHistorical(symbol: string): Observable<any> {
  // Example: function=TIME_SERIES_DAILY_ADJUSTED
  // More info: https://www.alphavantage.co/documentation/#dailyadj
  const url = `${this.baseUrl}?function=TIME_SERIES_DAILY&symbol=${symbol}&apikey=${this.apiKey}&datatype=json`;
  return this.http.get<any>(url);
}

getCurrentPrice(symbol: string): Observable<any> {
  const url = `${this.baseUrl}?function=GLOBAL_QUOTE&symbol=${symbol}&apikey=${this.apiKey}`;
  return this.http.get<any>(url);
}

}