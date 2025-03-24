import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BuyersBarComponent } from './buyers-bar.component';

describe('BuyersBarComponent', () => {
  let component: BuyersBarComponent;
  let fixture: ComponentFixture<BuyersBarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BuyersBarComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BuyersBarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
