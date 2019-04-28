import {Component, OnInit} from '@angular/core';
import {LanService, UserService, Lan} from 'core';
import {MatDialog} from '@angular/material';
import {CreateLanComponent} from '../create-lan/create-lan.component';

@Component({
  selector: 'app-landing',
  templateUrl: 'landing.component.html',
  styleUrls: ['landing.component.css']
})
/**
 * Page principale après la connexion
 */
export class LandingComponent implements OnInit {

  // LAN de l'application
  lans: Lan[];

  // Si les LANs de l'application on été chargés
  lansLoaded = false;

  // LAN courant
  currentLan: Lan;

  // Si le LAN courant est en cours de changement
  isChangingCurrentLan = false;

  constructor(
    private userService: UserService,
    private lanService: LanService,
    public dialog: MatDialog
  ) {
  }

  ngOnInit(): void {
    this.getLans();
  }

  /**
   * Rendre un LAN courant.
   */
  setCurrentLan(lanId: number) {
    if (lanId !== this.currentLan.id) {
      this.isChangingCurrentLan = true;
      this.lanService.setCurrentLan(lanId).subscribe((data: Lan) => {
        this.isChangingCurrentLan = false;
        this.currentLan = data;
      });
    }
  }

  getLans(): void {
    // Obtenir les LANs de l'application
    this.lanService.getAll()
      .subscribe(lans => {
        this.lans = lans;
        this.lansLoaded = true;
        this.currentLan = lans.find(lan => lan.isCurrent);
      });
  }

  openCreateLanDialog() {
    const dialogRef = this.dialog.open(CreateLanComponent, {
      width: '1000px',
      maxWidth: '90vw'
    });

    dialogRef.afterClosed().subscribe(() => {
      this.getLans();
      this.lanService.getLan().subscribe();
    });
  }

}
