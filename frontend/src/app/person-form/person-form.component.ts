import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../services/api.service';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-person-form',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './person-form.component.html',
  styleUrls: ['./person-form.component.css']
})
export class PersonFormComponent {
  @Output() employmentAdded = new EventEmitter<void>();

  person = {
    nom: '',
    prenom: '',
    dateNaissance: ''
  };

  employment = {
    personId: null as number | null,
    nomEntreprise: '',
    poste: '',
    dateDebut: '',
    dateFin: ''
  };

  persons: any[] = [];
  error: string | null = null;
  success: string | null = null;

  constructor(private apiService: ApiService) {
    this.loadPersons();
  }

  loadPersons(): void {
    this.apiService.getPersons().subscribe({
      next: (data) => {
        this.persons = data;
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de la récupération des personnes : ' + err.message;
      }
    });
  }

  createPerson(): void {
    this.error = null;
    this.success = null;

    this.apiService.createPerson(this.person).subscribe({
      next: (response) => {
        this.success = 'Personne créée avec succès !';
        this.person = { nom: '', prenom: '', dateNaissance: '' };
        this.loadPersons();
        this.employmentAdded.emit();
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de la création de la personne : ' + err.message;
      }
    });
  }

  createEmployment(): void {
    if (!this.employment.personId) {
      this.error = 'Veuillez sélectionner une personne.';
      return;
    }

    this.error = null;
    this.success = null;

    this.apiService.createEmployment(this.employment.personId, this.employment).subscribe({
      next: (response) => {
        this.success = 'Emploi ajouté avec succès !';
        this.employment = { personId: null, nomEntreprise: '', poste: '', dateDebut: '', dateFin: '' };
        this.employmentAdded.emit(); // Émet l'événement après ajout d'un emploi
      },
      error: (err: HttpErrorResponse) => {
        this.error = 'Erreur lors de l’ajout de l’emploi : ' + err.message;
      }
    });
  }
}