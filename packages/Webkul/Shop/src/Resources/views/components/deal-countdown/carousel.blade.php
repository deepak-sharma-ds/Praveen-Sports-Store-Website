<v-deal-countdown image="{{ bagisto_asset('images/deal-banner.png') }}" title="Don't Miss the Deal"
    subtitle="Score big savings on all your favorites" button-url="/collections/sale" button-text="Shop Deals"
    note="Cannot be combined with any other offer" end-date="2025-12-31T00:00:00"></v-deal-countdown>

@pushOnce('scripts')
    <script type="text/x-template" id="v-deal-countdown-template">
        <div class="relative w-full overflow-hidden rounded-2xl shadow-md">
            <!-- Background Image -->
            <img
                :src="image"
                alt="Deal Banner"
                class="w-full h-[540px] object-cover"
            />

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/30"></div>

            <!-- Countdown Box -->
            <div
                class="absolute top-1/2 right-10 -translate-y-1/2 z-10 w-[300px] p-6 rounded-xl text-white text-center shadow-lg"
                style="background: linear-gradient(135deg, #9c27b0 0%, #3f51b5 100%);"
            >
                <h2 class="text-xl font-bold uppercase mb-1" v-text="title"></h2>
                <p class="text-xs mb-3 tracking-wide" v-text="subtitle"></p>

                <!-- Countdown Timer -->
                <div class="grid grid-cols-4 gap-1 text-center mb-4">
                    <div>
                        <p class="text-2xl font-bold" v-text="time.days"></p>
                        <p class="text-[10px] uppercase">Days</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" v-text="time.hours"></p>
                        <p class="text-[10px] uppercase">Hours</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" v-text="time.minutes"></p>
                        <p class="text-[10px] uppercase">Mins</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" v-text="time.seconds"></p>
                        <p class="text-[10px] uppercase">Secs</p>
                    </div>
                </div>

                <!-- CTA Button -->
                <a
                    :href="buttonUrl"
                    class="inline-block bg-white text-purple-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100 transition"
                    v-text="buttonText"
                ></a>

                <p class="text-[10px] mt-2 opacity-80 italic">
                    *Cannot be combined with any other offer
                </p>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-deal-countdown', {
            template: '#v-deal-countdown-template',

            props: {
                image: String,
                title: String,
                subtitle: String,
                buttonUrl: String,
                buttonText: String,
                note: String,
                endDate: String,
            },

            data() {
                return {
                    time: {
                        days: 0,
                        hours: 0,
                        minutes: 0,
                        seconds: 0
                    },
                    timer: null,
                };
            },

            mounted() {
                this.startCountdown();
            },

            beforeUnmount() {
                if (this.timer) clearInterval(this.timer);
            },

            methods: {
                startCountdown() {
                    const target = this.endDate ?
                        new Date(this.endDate).getTime() :
                        new Date().getTime() + 50 * 24 * 60 * 60 * 1000;

                    this.timer = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = target - now;

                        if (distance <= 0) {
                            clearInterval(this.timer);
                            return;
                        }

                        this.time.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        this.time.hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                        this.time.minutes = Math.floor((distance / (1000 * 60)) % 60);
                        this.time.seconds = Math.floor((distance / 1000) % 60);
                    }, 1000);
                },
            },
        });
    </script>
@endPushOnce
