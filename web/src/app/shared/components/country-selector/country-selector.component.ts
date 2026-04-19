import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

interface Country {
  name: string;
  code: string;
  dialCode: string;
}

@Component({
  selector: 'app-country-selector',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './country-selector.component.html',
  styleUrl: './country-selector.component.scss'
})
export class CountrySelectorComponent implements OnInit {
  @Input() selectedCountry: Country | null = null;
  @Output() countrySelected = new EventEmitter<Country>();

  showDropdown = false;
  searchTerm = '';
  filteredCountries: Country[] = [];

  countries: Country[] = [
    // Africa
    { name: 'Algeria', code: 'DZ', dialCode: '+213' },
    { name: 'Angola', code: 'AO', dialCode: '+244' },
    { name: 'Botswana', code: 'BW', dialCode: '+267' },
    { name: 'Egypt', code: 'EG', dialCode: '+20' },
    { name: 'Ethiopia', code: 'ET', dialCode: '+251' },
    { name: 'Ghana', code: 'GH', dialCode: '+233' },
    { name: 'Kenya', code: 'KE', dialCode: '+254' },
    { name: 'Lesotho', code: 'LS', dialCode: '+266' },
    { name: 'Libya', code: 'LY', dialCode: '+218' },
    { name: 'Morocco', code: 'MA', dialCode: '+212' },
    { name: 'Mozambique', code: 'MZ', dialCode: '+258' },
    { name: 'Namibia', code: 'NA', dialCode: '+264' },
    { name: 'Nigeria', code: 'NG', dialCode: '+234' },
    { name: 'South Africa', code: 'ZA', dialCode: '+27' },
    { name: 'Swaziland', code: 'SZ', dialCode: '+268' },
    { name: 'Tanzania', code: 'TZ', dialCode: '+255' },
    { name: 'Tunisia', code: 'TN', dialCode: '+216' },
    { name: 'Uganda', code: 'UG', dialCode: '+256' },
    { name: 'Zambia', code: 'ZM', dialCode: '+260' },
    { name: 'Zimbabwe', code: 'ZW', dialCode: '+263' },
    
    // Asia
    { name: 'Afghanistan', code: 'AF', dialCode: '+93' },
    { name: 'Bangladesh', code: 'BD', dialCode: '+880' },
    { name: 'Cambodia', code: 'KH', dialCode: '+855' },
    { name: 'China', code: 'CN', dialCode: '+86' },
    { name: 'India', code: 'IN', dialCode: '+91' },
    { name: 'Indonesia', code: 'ID', dialCode: '+62' },
    { name: 'Iran', code: 'IR', dialCode: '+98' },
    { name: 'Iraq', code: 'IQ', dialCode: '+964' },
    { name: 'Israel', code: 'IL', dialCode: '+972' },
    { name: 'Japan', code: 'JP', dialCode: '+81' },
    { name: 'Jordan', code: 'JO', dialCode: '+962' },
    { name: 'Kazakhstan', code: 'KZ', dialCode: '+7' },
    { name: 'Kuwait', code: 'KW', dialCode: '+965' },
    { name: 'Lebanon', code: 'LB', dialCode: '+961' },
    { name: 'Malaysia', code: 'MY', dialCode: '+60' },
    { name: 'Mongolia', code: 'MN', dialCode: '+976' },
    { name: 'Myanmar', code: 'MM', dialCode: '+95' },
    { name: 'Nepal', code: 'NP', dialCode: '+977' },
    { name: 'North Korea', code: 'KP', dialCode: '+850' },
    { name: 'Pakistan', code: 'PK', dialCode: '+92' },
    { name: 'Philippines', code: 'PH', dialCode: '+63' },
    { name: 'Qatar', code: 'QA', dialCode: '+974' },
    { name: 'Saudi Arabia', code: 'SA', dialCode: '+966' },
    { name: 'Singapore', code: 'SG', dialCode: '+65' },
    { name: 'South Korea', code: 'KR', dialCode: '+82' },
    { name: 'Sri Lanka', code: 'LK', dialCode: '+94' },
    { name: 'Syria', code: 'SY', dialCode: '+963' },
    { name: 'Taiwan', code: 'TW', dialCode: '+886' },
    { name: 'Thailand', code: 'TH', dialCode: '+66' },
    { name: 'Turkey', code: 'TR', dialCode: '+90' },
    { name: 'United Arab Emirates', code: 'AE', dialCode: '+971' },
    { name: 'Uzbekistan', code: 'UZ', dialCode: '+998' },
    { name: 'Vietnam', code: 'VN', dialCode: '+84' },
    { name: 'Yemen', code: 'YE', dialCode: '+967' },
    
    // Europe
    { name: 'Albania', code: 'AL', dialCode: '+355' },
    { name: 'Austria', code: 'AT', dialCode: '+43' },
    { name: 'Belarus', code: 'BY', dialCode: '+375' },
    { name: 'Belgium', code: 'BE', dialCode: '+32' },
    { name: 'Bosnia and Herzegovina', code: 'BA', dialCode: '+387' },
    { name: 'Bulgaria', code: 'BG', dialCode: '+359' },
    { name: 'Croatia', code: 'HR', dialCode: '+385' },
    { name: 'Czech Republic', code: 'CZ', dialCode: '+420' },
    { name: 'Denmark', code: 'DK', dialCode: '+45' },
    { name: 'Estonia', code: 'EE', dialCode: '+372' },
    { name: 'Finland', code: 'FI', dialCode: '+358' },
    { name: 'France', code: 'FR', dialCode: '+33' },
    { name: 'Germany', code: 'DE', dialCode: '+49' },
    { name: 'Greece', code: 'GR', dialCode: '+30' },
    { name: 'Hungary', code: 'HU', dialCode: '+36' },
    { name: 'Iceland', code: 'IS', dialCode: '+354' },
    { name: 'Ireland', code: 'IE', dialCode: '+353' },
    { name: 'Italy', code: 'IT', dialCode: '+39' },
    { name: 'Latvia', code: 'LV', dialCode: '+371' },
    { name: 'Lithuania', code: 'LT', dialCode: '+370' },
    { name: 'Luxembourg', code: 'LU', dialCode: '+352' },
    { name: 'Malta', code: 'MT', dialCode: '+356' },
    { name: 'Moldova', code: 'MD', dialCode: '+373' },
    { name: 'Montenegro', code: 'ME', dialCode: '+382' },
    { name: 'Netherlands', code: 'NL', dialCode: '+31' },
    { name: 'North Macedonia', code: 'MK', dialCode: '+389' },
    { name: 'Norway', code: 'NO', dialCode: '+47' },
    { name: 'Poland', code: 'PL', dialCode: '+48' },
    { name: 'Portugal', code: 'PT', dialCode: '+351' },
    { name: 'Romania', code: 'RO', dialCode: '+40' },
    { name: 'Russia', code: 'RU', dialCode: '+7' },
    { name: 'Serbia', code: 'RS', dialCode: '+381' },
    { name: 'Slovakia', code: 'SK', dialCode: '+421' },
    { name: 'Slovenia', code: 'SI', dialCode: '+386' },
    { name: 'Spain', code: 'ES', dialCode: '+34' },
    { name: 'Sweden', code: 'SE', dialCode: '+46' },
    { name: 'Switzerland', code: 'CH', dialCode: '+41' },
    { name: 'Ukraine', code: 'UA', dialCode: '+380' },
    { name: 'United Kingdom', code: 'GB', dialCode: '+44' },
    
    // North America
    { name: 'Canada', code: 'CA', dialCode: '+1' },
    { name: 'Costa Rica', code: 'CR', dialCode: '+506' },
    { name: 'Cuba', code: 'CU', dialCode: '+53' },
    { name: 'Dominican Republic', code: 'DO', dialCode: '+1' },
    { name: 'El Salvador', code: 'SV', dialCode: '+503' },
    { name: 'Guatemala', code: 'GT', dialCode: '+502' },
    { name: 'Haiti', code: 'HT', dialCode: '+509' },
    { name: 'Honduras', code: 'HN', dialCode: '+504' },
    { name: 'Jamaica', code: 'JM', dialCode: '+1' },
    { name: 'Mexico', code: 'MX', dialCode: '+52' },
    { name: 'Nicaragua', code: 'NI', dialCode: '+505' },
    { name: 'Panama', code: 'PA', dialCode: '+507' },
    { name: 'United States', code: 'US', dialCode: '+1' },
    
    // South America
    { name: 'Argentina', code: 'AR', dialCode: '+54' },
    { name: 'Bolivia', code: 'BO', dialCode: '+591' },
    { name: 'Brazil', code: 'BR', dialCode: '+55' },
    { name: 'Chile', code: 'CL', dialCode: '+56' },
    { name: 'Colombia', code: 'CO', dialCode: '+57' },
    { name: 'Ecuador', code: 'EC', dialCode: '+593' },
    { name: 'French Guiana', code: 'GF', dialCode: '+594' },
    { name: 'Guyana', code: 'GY', dialCode: '+592' },
    { name: 'Paraguay', code: 'PY', dialCode: '+595' },
    { name: 'Peru', code: 'PE', dialCode: '+51' },
    { name: 'Suriname', code: 'SR', dialCode: '+597' },
    { name: 'Uruguay', code: 'UY', dialCode: '+598' },
    { name: 'Venezuela', code: 'VE', dialCode: '+58' },
    
    // Oceania
    { name: 'Australia', code: 'AU', dialCode: '+61' },
    { name: 'Fiji', code: 'FJ', dialCode: '+679' },
    { name: 'New Zealand', code: 'NZ', dialCode: '+64' },
    { name: 'Papua New Guinea', code: 'PG', dialCode: '+675' },
    { name: 'Samoa', code: 'WS', dialCode: '+685' },
    { name: 'Tonga', code: 'TO', dialCode: '+676' },
    { name: 'Vanuatu', code: 'VU', dialCode: '+678' }
  ];

  ngOnInit() {
    this.filteredCountries = [...this.countries];
  }

  onSearchInput(event: any) {
    this.searchTerm = event.target.value;
    this.filterCountries();
    this.showDropdown = true;
  }

  onFocus() {
    this.showDropdown = true;
    if (!this.searchTerm) {
      this.filteredCountries = [...this.countries];
    }
  }

  onBlur() {
    // Delay hiding dropdown to allow click events
    setTimeout(() => {
      this.showDropdown = false;
    }, 200);
  }

  selectCountry(country: Country) {
    this.selectedCountry = country;
    this.searchTerm = ''; // Clear search term to show selected country name
    this.countrySelected.emit(country);
    this.showDropdown = false;
  }

  filterCountries() {
    if (!this.searchTerm) {
      this.filteredCountries = [...this.countries];
    } else {
      this.filteredCountries = this.countries.filter(country =>
        country.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        country.dialCode.includes(this.searchTerm)
      );
    }
  }
}