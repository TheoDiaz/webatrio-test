import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';
import { HttpErrorResponse } from '@angular/common/http';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-person-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './person-list.component.html',
  styleUrls: ['./person-list.component.css']
})
export class PersonListComponent implements OnInit {
  persons: any[] = [];
  errorPersons: string | null = null;
  loadingPersons = false;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadPersons();
  }

  loadPersons(): void {
    this.loadingPersons = true;
    this.persons = [];
    this.errorPersons = null;

    this.apiService.getPersons().subscribe({
      next: (data: any[]) => {
        this.persons = data;
        this.loadingPersons = false;
      },
      error: (err: HttpErrorResponse) => {
        this.errorPersons = 'Erreur lors de la récupération des personnes : ' + err.message;
        this.loadingPersons = false;
      }
    });
  }
}
