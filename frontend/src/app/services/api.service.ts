import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  getPersons(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/persons`);
  }

  getEmploymentsByCompany(company: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/employments?company=${company}`);
  }

  createPerson(person: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/persons`, person);
  }

  createEmployment(personId: number, employment: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/persons/${personId}/employments`, employment);
  }

  getCompanies(): Observable<string[]> {
    return this.http.get<string[]>(`${this.apiUrl}/companies`);
  }
  
  getEmploymentsBetweenDates(personId: number, startDate: string, endDate: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/persons/${personId}/employments/between-dates?startDate=${startDate}&endDate=${endDate}`);
  }
}