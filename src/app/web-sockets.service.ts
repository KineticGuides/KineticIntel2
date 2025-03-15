import { Injectable } from '@angular/core';
import { webSocket, WebSocketSubject } from 'rxjs/webSocket';

@Injectable({
        providedIn: 'root',
})
export class WebSocketService {
        private socket$!: WebSocketSubject<any>;

        constructor() {
          this.connect();
        }

        connect(): void {
          this.socket$ = webSocket('wss://wss.sqllabs.ai/websocket');
          console.log('connect')
        }

        sendMessage(message: any): void {
          this.socket$.next(message);
        }

        onMessage() {
          console.log('onMessage')
          return this.socket$.asObservable();
        }

        close(): void {
          this.socket$.complete();
        } 
}
