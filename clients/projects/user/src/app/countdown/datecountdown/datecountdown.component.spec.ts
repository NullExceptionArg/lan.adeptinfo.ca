import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DatecountdownComponent } from './datecountdown.component';

describe('DatecountdownComponent', () => {
  let component: DatecountdownComponent;
  let fixture: ComponentFixture<DatecountdownComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DatecountdownComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DatecountdownComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
