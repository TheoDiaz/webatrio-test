import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';
import { HttpErrorResponse } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-employment-list',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './employment-list.component.html',
  styleUrls: ['./employment-list.component.css']
})
export class EmploymentListComponent implements OnInit {
  companies: string[] = [];
  selectedCompany: string = '';
  employments: any[] = [];
  error: string | null = null;
  loading = false;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadCompanies();
  }

  loadCompanies(): void {
    this.apiService.getCompanies().subscribe({
      next: (data: string[]) => {
        this.companies = data;
        if (data.length > 0 && !this.companies.includes(this.selectedCompany)) {
          this.selectedCompany = data[0];
          this.loadEmployments();
        }
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de la récupération des entreprises : ' + err.message;
      }
    });
  }

  loadEmployments(): void {
    if (!this.selectedCompany) return;

    this.loading = true;
    this.employments = [];
    this.error = null;

    this.apiService.getEmploymentsByCompany(this.selectedCompany).subscribe({
      next: (data: any[]) => {
        this.employments = data;
        this.loading = false;
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de la récupération des emplois : ' + err.message;
        this.loading = false;
      }
    });
  }

  onCompanyChange(): void {
    this.loadEmployments();
  }
}
