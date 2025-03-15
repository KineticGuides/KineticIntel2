import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LostPositonsComponent } from './lost-positons.component';

describe('LostPositonsComponent', () => {
  let component: LostPositonsComponent;
  let fixture: ComponentFixture<LostPositonsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LostPositonsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(LostPositonsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
