import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CallNotesComponent } from './call-notes.component';

describe('CallNotesComponent', () => {
  let component: CallNotesComponent;
  let fixture: ComponentFixture<CallNotesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CallNotesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CallNotesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
