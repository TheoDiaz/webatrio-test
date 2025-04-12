import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-employment-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './employment-list.component.html',
  styleUrls: ['./employment-list.component.css']
})
export class EmploymentListComponent implements OnInit, OnChanges {
  @Input() company: string = 'TechCorp';
  employments: any[] = [];
  loadingEmployments = true;
  errorEmployments: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadEmployments();
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['company'] && changes['company'].currentValue) {
      this.loadEmployments();
    }
  }

  loadEmployments(): void {
    this.loadingEmployments = true;
    this.errorEmployments = null;
    this.employments = [];

    this.apiService.getEmploymentsByCompany(this.company).subscribe({
      next: (data) => {
        console.log('Employments data:', data);
        this.employments = data;
        this.loadingEmployments = false;
      },
      error: (err) => {
        console.error('Error fetching employments:', err);
        this.errorEmployments = 'Erreur lors de la récupération des emplois : ' + err.message;
        this.loadingEmployments = false;
      }
    });
  }
}