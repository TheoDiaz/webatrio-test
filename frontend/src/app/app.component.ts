import { Component, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PersonListComponent } from './person-list/person-list.component';
import { EmploymentListComponent } from './employment-list/employment-list.component';
import { PersonFormComponent } from './person-form/person-form.component';
import { EmploymentHistoryComponent } from './employment-history/employment-history.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    PersonListComponent,
    EmploymentListComponent,
    PersonFormComponent,
    EmploymentHistoryComponent
  ],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  @ViewChild(PersonListComponent) personListComponent!: PersonListComponent;
  @ViewChild(EmploymentListComponent) employmentListComponent!: EmploymentListComponent;

  onEmploymentAdded(): void {
    this.personListComponent.loadPersons();
    this.employmentListComponent.loadEmployments();
  }
}
