import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewPositonsComponent } from './new-positons.component';

describe('NewPositonsComponent', () => {
  let component: NewPositonsComponent;
  let fixture: ComponentFixture<NewPositonsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NewPositonsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(NewPositonsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
