export class LocationIQSearchHelper {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.baseUrl = 'https://us1.locationiq.com/v1/search.php';
    }

    async searchAddress(query) {
        const response = await fetch(`${this.baseUrl}?key=${this.apiKey}&q=${query}&format=json`);
        if (!response.ok) throw new Error('Error al buscar la dirección');
        return await response.json();
    }
}
