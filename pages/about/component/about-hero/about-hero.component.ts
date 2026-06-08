import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
  selector: 'app-about-hero',
  imports: [TranslatePipe],
  templateUrl: './about-hero.component.html',
  styleUrl: './about-hero.component.css',
})
export class AboutHeroComponent {}
