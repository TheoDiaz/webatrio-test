import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EmploymentListComponent } from './employment-list.component';

describe('EmploymentListComponent', () => {
  let component: EmploymentListComponent;
  let fixture: ComponentFixture<EmploymentListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [EmploymentListComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EmploymentListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
