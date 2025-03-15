import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewParticipantsComponent } from './new-participants.component';

describe('NewParticipantsComponent', () => {
  let component: NewParticipantsComponent;
  let fixture: ComponentFixture<NewParticipantsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NewParticipantsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(NewParticipantsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
