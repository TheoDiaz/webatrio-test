import { Component, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PersonListComponent } from './person-list/person-list.component';
import { EmploymentListComponent } from './employment-list/employment-list.component';
import { PersonFormComponent } from './person-form/person-form.component';
import { ApiService } from './services/api.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    PersonListComponent,
    EmploymentListComponent,
    PersonFormComponent
  ],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  @ViewChild(PersonListComponent) personListComponent!: PersonListComponent;
  @ViewChild(EmploymentListComponent) employmentListComponent!: EmploymentListComponent;

  companies: string[] = [];
  selectedCompany: string = '';
  error: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadCompanies();
  }

  loadCompanies(): void {
    this.apiService.getCompanies().subscribe({
      next: (data) => {
        console.log('Companies loaded:', data);
        this.companies = data;
        if (data.length > 0 && !this.companies.includes(this.selectedCompany)) {
          this.selectedCompany = data[0];
        }
      },
      error: (err) => {
        this.error = 'Erreur lors de la récupération des entreprises : ' + err.message;
      }
    });
  }

  onEmploymentAdded(): void {
    this.loadCompanies();
    this.personListComponent.loadPersons();
    this.employmentListComponent.loadEmployments();
  }
}