import {Component, OnInit} from '@angular/core';
import {Lan} from '../core/models/lan';
import {UserService} from '../core/services/user.service';
import {LanService} from '../core/services/lan.service';
import {MatDialog} from '@angular/material';
import {CreateLanComponent} from '../lan/create-lan/create-lan.component';

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

  constructor(
    private userService: UserService,
    private lanService: LanService,
    public dialog: MatDialog
  ) {
  }

  ngOnInit(): void {
    // Obtenir les LANs de l'application
    this.lanService.getAll()
      .subscribe(lans => {
        this.lans = lans;
        this.lansLoaded = true;
        this.currentLan = lans.find(lan => lan.is_current);
      });
    // this.openCreateLanDialog();
  }

  /**
   * Rendre un LAN courant.
   */
  setCurrentLan() {
    this.lanService.setCurrentLan(this.currentLan);
  }

  openCreateLanDialog() {
    const dialogRef = this.dialog.open(CreateLanComponent, {
      width: '1000px',
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log('The dialog was closed');
    });
  }

}
