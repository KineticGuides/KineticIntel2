import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StaticBarComponent } from './static-bar.component';

describe('StaticBarComponent', () => {
  let component: StaticBarComponent;
  let fixture: ComponentFixture<StaticBarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StaticBarComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(StaticBarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
