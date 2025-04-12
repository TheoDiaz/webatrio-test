import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-person-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './person-list.component.html',
  styleUrls: ['./person-list.component.css']
})
export class PersonListComponent implements OnInit {
  persons: any[] = [];
  loadingPersons = true;
  errorPersons: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadPersons();
  }

  loadPersons(): void { // Ajout de la méthode manquante
    this.loadingPersons = true;
    this.errorPersons = null;
    this.persons = [];

    this.apiService.getPersons().subscribe({
      next: (data) => {
        console.log('Persons data:', data);
        this.persons = data;
        this.loadingPersons = false;
      },
      error: (err) => {
        console.error('Error fetching persons:', err);
        this.errorPersons = 'Erreur lors de la récupération des personnes : ' + err.message;
        this.loadingPersons = false;
      }
    });
  }
}