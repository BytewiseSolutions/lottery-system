import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { LotteryService } from '../services/lottery.service';
import { AlertController, LoadingController } from '@ionic/angular';
import { ToastService } from '../services/toast.service';
import { environment } from '../../environments/environment';

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
  showSuccessModal = false;
  submittedNumbers: number[] = [];
  submittedBonusNumbers: number[] = [];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private lotteryService: LotteryService,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController,
    private toast: ToastService
  ) {}

  ngOnInit() {
    this.lottery = history.state.lottery;
    
    // Generate numbers 1-75 regardless
    for (let i = 1; i <= 75; i++) {
      this.numbers.push(i);
    }
    
    if (!this.lottery) {
      console.warn('No lottery data');
      // Don't redirect immediately, let user see the error when they try to submit
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
    
    if (!this.lottery) {
      this.toast.showError('Lottery data is missing. Please go back and try again.');
      return;
    }

    const loading = await this.loadingCtrl.create({ message: 'Submitting entry...' });
    await loading.present();

    const token = localStorage.getItem('token');
    
    try {
      const payload = {
        lottery: this.lottery.name.replace(' Lotto', '').toLowerCase(),
        numbers: this.selectedNumbers,
        bonusNumbers: this.selectedBonusNumbers,
        drawDate: this.lottery.draw_date
      };
      
      console.log('Submitting entry:', payload);
      
      const response = await fetch(`${environment.apiUrl}/api/play`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(payload)
      });

      const result = await response.json();
      console.log('Response:', response.status, result);

      if (result.requireHumanVerification) {
        loading.dismiss();
        this.toast.showError('You have played multiple times today. Please try again later.');
        return;
      }

      if (response.ok && result.success) {
        await loading.dismiss();
        this.submittedNumbers = [...this.selectedNumbers];
        this.submittedBonusNumbers = [...this.selectedBonusNumbers];
        this.showSuccessModal = true;
        this.toast.showSuccess('Entry submitted successfully!');
        window.dispatchEvent(new CustomEvent('entrySubmitted'));
        setTimeout(() => this.closeSuccessModal(), 5000);
      } else {
        loading.dismiss();
        this.toast.showError(result.error || 'Failed to submit entry');
      }
    } catch (error) {
      loading.dismiss();
      this.toast.showError('Network error. Please check your connection and try again.');
    }
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

  closeSuccessModal() {
    this.showSuccessModal = false;
    this.router.navigate(['/home']);
  }
}
