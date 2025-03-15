import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ActiveCallPageComponent } from './active-call-page.component';

describe('ActiveCallPageComponent', () => {
  let component: ActiveCallPageComponent;
  let fixture: ComponentFixture<ActiveCallPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ActiveCallPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ActiveCallPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
