<v-deal-countdown image="{{ bagisto_asset('images/deal-banner.png') }}" title="Hurry! The Deals Won’t Last" subtitle=""
    button-url="{{ route('shop.search.index', ['sort' => 'name-desc']) }}" button-text="Shop Deals"
    note="Cannot be combined with any other offer" end-date="2026-12-31T00:00:00"></v-deal-countdown>

@pushOnce('scripts')
    <script type="text/x-template" id="v-deal-countdown-template">
        <div class="relative w-full overflow-hidden z-0">
            <!-- Background Image -->
            <img
                :src="image"
                alt="Deal Banner"
                class="w-full h-[540px] object-cover"
            />

            <!-- Countdown Box -->
            <div
                class="absolute top-1/2 right-[10%] -translate-y-1/2 z-10 w-4/5 md:w-[440px] py-6 px-6 lg:px-12 rounded-xl text-white text-center shadow-lg bg-gradient-to-tr from-[#0F1D71] to-[#902129]">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-secondary font-bold uppercase mb-2" v-text="title"></h2>
                <p class="text-sm md:text-base font-medium uppercase mb-2 md:mb-5" v-text="subtitle"></p>

                <!-- Countdown Timer -->
                <!-- <div class="grid grid-cols-4 gap-1 text-center mb-4 text-3xl md:text-[44px] font-semibold">
                    <div>
                        <p v-text="time.days"></p>
                        <p class="text-sm md:text-base font-normal">Days</p>
                    </div>
                    <div>
                        <p v-text="time.hours"></p>
                        <p class="text-sm md:text-base font-normal">Hours</p>
                    </div>
                    <div>
                        <p v-text="time.minutes"></p>
                        <p class="text-sm md:text-base font-normal">Mins</p>
                    </div>
                    <div>
                        <p v-text="time.seconds"></p>
                        <p class="text-sm md:text-base font-normal">Secs</p>
                    </div>
                </div> -->

                <div class="pd-5">
                    <p class="text-base mt-2 font-medium">
                        Shop Deals Before Time Runs Out!
                    </p>
                </div>

                <!-- CTA Button -->
                <a
                    :href="buttonUrl"
                    class="inline-flex bg-white text-[#902129] rounded py-2.5 px-4 border border-[#AC153A] font-medium text-base uppercase transition-all hover:bg-[#AC153A] hover:text-white"
                    v-text="buttonText"
                ></a>
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
