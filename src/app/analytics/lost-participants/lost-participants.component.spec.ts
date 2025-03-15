import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LostParticipantsComponent } from './lost-participants.component';

describe('LostParticipantsComponent', () => {
  let component: LostParticipantsComponent;
  let fixture: ComponentFixture<LostParticipantsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LostParticipantsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(LostParticipantsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
