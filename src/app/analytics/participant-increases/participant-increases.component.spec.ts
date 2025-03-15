import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ParticipantIncreasesComponent } from './participant-increases.component';

describe('ParticipantIncreasesComponent', () => {
  let component: ParticipantIncreasesComponent;
  let fixture: ComponentFixture<ParticipantIncreasesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ParticipantIncreasesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ParticipantIncreasesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
