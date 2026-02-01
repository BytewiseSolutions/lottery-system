import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { LotteryService } from '../services/lottery.service';
import { AlertController, LoadingController } from '@ionic/angular';

@Component({
  selector: 'app-play',
  templateUrl: './play.page.html',
  styleUrls: ['./play.page.scss'],
  standalone: false
})
export class PlayPage implements OnInit {
  numbers: number[] = [];
  selectedNumbers: number[] = [];
  selectedBonusNumbers: number[] = [];
  lottery: any;
  currentStep = 1;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private lotteryService: LotteryService,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    this.lottery = history.state.lottery;
    if (!this.lottery) {
      this.router.navigate(['/home']);
      return;
    }
    
    for (let i = 1; i <= 75; i++) {
      this.numbers.push(i);
    }
  }

  toggleNumber(num: number) {
    const index = this.selectedNumbers.indexOf(num);
    if (index > -1) {
      this.selectedNumbers.splice(index, 1);
    } else if (this.selectedNumbers.length < 5) {
      this.selectedNumbers.push(num);
      this.selectedNumbers.sort((a, b) => a - b);
    }
  }

  toggleBonusNumber(num: number) {
    const index = this.selectedBonusNumbers.indexOf(num);
    if (index > -1) {
      this.selectedBonusNumbers.splice(index, 1);
    } else if (this.selectedBonusNumbers.length < 2) {
      this.selectedBonusNumbers.push(num);
      this.selectedBonusNumbers.sort((a, b) => a - b);
    }
  }

  nextStep() {
    if (this.currentStep === 1 && this.selectedNumbers.length === 5) {
      this.currentStep = 2;
    }
  }

  previousStep() {
    if (this.currentStep === 2) {
      this.currentStep = 1;
    }
  }

  async submitEntry() {
    if (this.selectedNumbers.length !== 5 || this.selectedBonusNumbers.length !== 2) {
      return;
    }

    const loading = await this.loadingCtrl.create({ message: 'Submitting entry...' });
    await loading.present();

    this.lotteryService.playLottery(this.lottery.id, [...this.selectedNumbers, ...this.selectedBonusNumbers]).subscribe({
      next: async (response) => {
        loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Success!',
          message: 'Your entry has been submitted successfully!',
          buttons: [{
            text: 'OK',
            handler: () => {
              this.router.navigate(['/home']);
            }
          }]
        });
        await alert.present();
      },
      error: async (err) => {
        loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Error',
          message: err.error?.error || 'Failed to submit entry',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  quickPick() {
    this.selectedNumbers = [];
    const available = [...this.numbers];
    for (let i = 0; i < 5; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedNumbers.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    this.selectedNumbers.sort((a, b) => a - b);
  }

  quickPickBonus() {
    this.selectedBonusNumbers = [];
    const available = [...this.numbers];
    for (let i = 0; i < 2; i++) {
      const randomIndex = Math.floor(Math.random() * available.length);
      this.selectedBonusNumbers.push(available[randomIndex]);
      available.splice(randomIndex, 1);
    }
    this.selectedBonusNumbers.sort((a, b) => a - b);
  }
}
