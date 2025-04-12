import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';
import { HttpErrorResponse } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-employment-history',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './employment-history.component.html',
  styleUrls: ['./employment-history.component.css']
})
export class EmploymentHistoryComponent implements OnInit {
  persons: any[] = [];
  selectedPersonId: number | null = null;
  startDate: string = '';
  endDate: string = '';
  employments: any[] = [];
  error: string | null = null;
  loading = false;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadPersons();
  }

  loadPersons(): void {
    this.apiService.getPersons().subscribe({
      next: (data: any[]) => {
        this.persons = data;
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de la récupération des personnes : ' + err.message;
      }
    });
  }

  searchEmployments(): void {
    if (!this.selectedPersonId || !this.startDate || !this.endDate) {
      this.error = 'Veuillez remplir tous les champs.';
      return;
    }

    this.loading = true;
    this.employments = [];
    this.error = null;

    this.apiService.getEmploymentsBetweenDates(this.selectedPersonId, this.startDate, this.endDate).subscribe({
      next: (data: any[]) => {
        this.employments = data;
        this.loading = false;
      },
      error: (err: HttpErrorResponse) => {
        this.error = err.error?.error || 'Erreur lors de la récupération des emplois : ' + err.message;
        this.loading = false;
      }
    });
  }
}
