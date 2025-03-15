import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ActiveParticipantsComponent } from './active-participants.component';

describe('ActiveParticipantsComponent', () => {
  let component: ActiveParticipantsComponent;
  let fixture: ComponentFixture<ActiveParticipantsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ActiveParticipantsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ActiveParticipantsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
