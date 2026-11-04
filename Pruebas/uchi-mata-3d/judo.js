import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.180.0/examples/jsm/controls/OrbitControls.js';

const stage = document.querySelector('.technique-stage');
const canvas = document.querySelector('.technique-canvas');
const playbackButton = document.querySelector('.playback-button');
const replayButton = document.querySelector('.replay-button');
const cameraButton = document.querySelector('.camera-button');
const cameraLabel = document.querySelector('.camera-label');
const phaseLabel = document.querySelector('.stage-phase');
const detailLabel = document.querySelector('.stage-detail');
const stepLabel = document.querySelector('.stage-step');

const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.2;

const scene = new THREE.Scene();
scene.fog = new THREE.Fog(0x111d28, 8, 18);

const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 40);
camera.position.set(0.4, 2.1, 5.25);

const controls = new OrbitControls(camera, canvas);
const viewTarget = new THREE.Vector3(0, 1.1, 0);
controls.target.copy(viewTarget);
controls.enableDamping = true;
controls.dampingFactor = 0.07;
controls.minDistance = 4.5;
controls.maxDistance = 9;
controls.minPolarAngle = 0.45;
controls.maxPolarAngle = 1.35;
controls.enablePan = false;
controls.autoRotate = false;
controls.update();

scene.add(new THREE.HemisphereLight(0xdbeeff, 0x26313a, 2.2));
const keyLight = new THREE.DirectionalLight(0xfff5e9, 3.6);
keyLight.position.set(-3, 7, 5);
keyLight.castShadow = true;
keyLight.shadow.mapSize.set(1024, 1024);
keyLight.shadow.camera.left = -5;
keyLight.shadow.camera.right = 5;
keyLight.shadow.camera.top = 5;
keyLight.shadow.camera.bottom = -5;
scene.add(keyLight);
const rimLight = new THREE.PointLight(0x5aa8ff, 22, 10);
rimLight.position.set(2.7, 3.2, -2.6);
scene.add(rimLight);

const floor = new THREE.Mesh(
    new THREE.PlaneGeometry(120, 120),
    new THREE.MeshStandardMaterial({ color: 0xb7c3c9, roughness: 0.9 })
);
floor.rotation.x = -Math.PI / 2;
floor.position.y = -0.025;
floor.receiveShadow = true;
scene.add(floor);

const tatami = new THREE.Mesh(
    new THREE.PlaneGeometry(6.2, 4.4),
    new THREE.MeshStandardMaterial({ color: 0xc7d0d0, roughness: 0.94 })
);
tatami.rotation.x = -Math.PI / 2;
tatami.position.y = 0.002;
tatami.receiveShadow = true;
scene.add(tatami);

const boundaryMaterial = new THREE.MeshBasicMaterial({ color: 0xd7474c, transparent: true, opacity: 0.82 });
const boundaryLines = [];
for (const [width, depth] of [[6.2, 0.035], [6.2, 0.035], [0.035, 4.4], [0.035, 4.4]]) {
    const line = new THREE.Mesh(new THREE.PlaneGeometry(width, depth), boundaryMaterial);
    line.rotation.x = -Math.PI / 2;
    line.position.y = 0.009;
    boundaryLines.push(line);
    scene.add(line);
}
boundaryLines[0].position.z = -2.2;
boundaryLines[1].position.z = 2.2;
boundaryLines[2].position.x = -3.1;
boundaryLines[3].position.x = 3.1;

const skinMaterial = new THREE.MeshStandardMaterial({ color: 0xbd856b, roughness: 0.85 });
const hairMaterial = new THREE.MeshStandardMaterial({ color: 0x171416, roughness: 0.95 });
const eyeMaterial = new THREE.MeshStandardMaterial({ color: 0xf4f0e8, roughness: 0.78 });
const pupilMaterial = new THREE.MeshStandardMaterial({ color: 0x30231e, roughness: 0.72 });
const mouthMaterial = new THREE.MeshStandardMaterial({ color: 0x784d47, roughness: 0.9 });
const whiteMaterial = new THREE.MeshStandardMaterial({ color: 0xe8e6df, roughness: 0.98 });
const whiteTrim = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.95 });
const blueMaterial = new THREE.MeshStandardMaterial({ color: 0x1643a4, roughness: 0.96 });
const blueTrim = new THREE.MeshStandardMaterial({ color: 0x3462c3, roughness: 0.95 });
const beltMaterial = new THREE.MeshStandardMaterial({ color: 0x19191c, roughness: 0.95 });
const down = new THREE.Vector3(0, -1, 0);
const groundHeight = tatami.position.y + 0.012;

function makeFabricTexture() {
    const image = document.createElement('canvas');
    image.width = 128;
    image.height = 128;
    const context = image.getContext('2d');
    const pixels = context.createImageData(image.width, image.height);
    for (let y = 0; y < image.height; y++) {
        for (let x = 0; x < image.width; x++) {
            const weave = (x % 4 === 0 ? -9 : 0) + (y % 4 === 0 ? -6 : 0);
            const grain = ((x * 13 + y * 7) % 9) - 4;
            const value = 242 + weave + grain;
            const index = (y * image.width + x) * 4;
            pixels.data[index] = value;
            pixels.data[index + 1] = value;
            pixels.data[index + 2] = value;
            pixels.data[index + 3] = 255;
        }
    }
    context.putImageData(pixels, 0, 0);
    const texture = new THREE.CanvasTexture(image);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.set(4, 5);
    texture.anisotropy = renderer.capabilities.getMaxAnisotropy();
    return texture;
}

const fabricTexture = makeFabricTexture();
whiteMaterial.map = fabricTexture;
blueMaterial.map = fabricTexture;
whiteMaterial.bumpMap = fabricTexture;
blueMaterial.bumpMap = fabricTexture;
whiteMaterial.bumpScale = 0.018;
blueMaterial.bumpScale = 0.018;
whiteMaterial.needsUpdate = true;
blueMaterial.needsUpdate = true;

function mesh(geometry, material, parent, x = 0, y = 0, z = 0) {
    const part = new THREE.Mesh(geometry, material);
    part.position.set(x, y, z);
    part.castShadow = true;
    part.receiveShadow = true;
    parent.add(part);
    return part;
}

function ellipsoid(parent, material, position, scale) {
    const part = mesh(new THREE.SphereGeometry(1, 24, 16), material, parent, ...position);
    part.scale.set(...scale);
    return part;
}

// Flared hems and tapered sleeves give the uniform a cloth silhouette.
function cloth(parent, material, top, bottom, length, y, depth = 1) {
    const part = mesh(new THREE.CylinderGeometry(top, bottom, length, 24, 4), material, parent, 0, y);
    part.scale.z = depth;
    return part;
}

function makeJudoka(gi, trim) {
    const root = new THREE.Group();
    const rig = new THREE.Group();
    root.add(rig);
    ellipsoid(rig, gi, [0, -0.02, 0], [0.28, 0.2, 0.18]);
    const torso = new THREE.Group();
    torso.position.y = 0.07;
    rig.add(torso);
    // Adult proportions: broad shoulders, a flatter chest and a smaller head.
    const jacket = cloth(torso, gi, 0.34, 0.25, 0.59, 0.38, 0.61);
    ellipsoid(torso, gi, [0, 0.61, 0], [0.34, 0.13, 0.2]);
    cloth(torso, gi, 0.25, 0.33, 0.25, -0.035, 0.67);
    ellipsoid(torso, skinMaterial, [0, 0.65, 0.155], [0.1, 0.13, 0.035]);
    for (const side of [-1, 1]) {
        const lapel = mesh(new THREE.BoxGeometry(0.075, 0.53, 0.035), trim, torso, side * 0.095, 0.41, 0.205);
        lapel.rotation.z = side * -0.37;
        const tail = mesh(new THREE.BoxGeometry(0.065, 0.27, 0.035), gi, torso, side * 0.055, -0.06, 0.24);
        tail.rotation.z = side * 0.18;
    }
    cloth(torso, beltMaterial, 0.262, 0.267, 0.075, 0.105, 0.75);
    ellipsoid(torso, beltMaterial, [0, 0.105, 0.22], [0.075, 0.05, 0.04]);
    cloth(torso, skinMaterial, 0.08, 0.1, 0.15, 0.75);
    ellipsoid(torso, skinMaterial, [0, 0.93, 0], [0.15, 0.205, 0.155]);
    const hair = mesh(new THREE.SphereGeometry(1, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.55), hairMaterial, torso, 0, 0.97, -0.015);
    hair.scale.set(0.156, 0.177, 0.16);
    ellipsoid(torso, skinMaterial, [0, 0.92, 0.157], [0.032, 0.045, 0.04]);
    for (const side of [-1, 1]) {
        ellipsoid(torso, skinMaterial, [side * 0.15, 0.93, 0], [0.025, 0.046, 0.026]);
        ellipsoid(torso, hairMaterial, [side * 0.055, 0.965, 0.148], [0.013, 0.011, 0.01]);
        ellipsoid(torso, hairMaterial, [side * 0.055, 0.99, 0.144], [0.026, 0.007, 0.008]);
        ellipsoid(torso, eyeMaterial, [side * 0.052, 0.94, 0.149], [0.019, 0.012, 0.009]);
        ellipsoid(torso, pupilMaterial, [side * 0.052, 0.94, 0.157], [0.007, 0.009, 0.005]);
    }
    ellipsoid(torso, skinMaterial, [0, 0.925, 0.17], [0.022, 0.034, 0.026]);
    ellipsoid(torso, mouthMaterial, [0, 0.865, 0.158], [0.026, 0.005, 0.006]);
    const arms = [];
    for (const side of [-1, 1]) {
        const shoulder = new THREE.Group();
        shoulder.position.set(side * 0.32, 0.59, 0);
        torso.add(shoulder);
        ellipsoid(shoulder, gi, [0, -0.025, 0], [0.14, 0.14, 0.14]);
        cloth(shoulder, gi, 0.145, 0.115, 0.42, -0.21);
        const elbow = new THREE.Group();
        elbow.position.y = -0.42;
        shoulder.add(elbow);
        ellipsoid(elbow, gi, [0, 0, 0], [0.115, 0.11, 0.115]);
        cloth(elbow, gi, 0.115, 0.095, 0.32, -0.16);
        cloth(elbow, trim, 0.097, 0.1, 0.055, -0.31);
        const hand = ellipsoid(elbow, skinMaterial, [0, -0.4, 0], [0.065, 0.09, 0.045]);
        for (let finger = 0; finger < 4; finger++) {
            const spread = (finger - 1.5) * 0.025;
            ellipsoid(elbow, skinMaterial, [spread, -0.48 - (1.5 - Math.abs(finger - 1.5)) * 0.009, 0.008], [0.012, 0.043, 0.013]);
        }
        ellipsoid(elbow, skinMaterial, [side * 0.064, -0.405, 0.018], [0.018, 0.043, 0.015]);
        arms.push({ shoulder, elbow, hand });
    }
    const legs = [];
    for (const side of [-1, 1]) {
        const hip = new THREE.Group();
        hip.position.set(side * 0.165, -0.12, 0);
        rig.add(hip);
        cloth(hip, gi, 0.165, 0.135, 0.46, -0.23);
        const knee = new THREE.Group();
        knee.position.y = -0.46;
        hip.add(knee);
        ellipsoid(knee, gi, [0, 0, 0], [0.135, 0.12, 0.135]);
        cloth(knee, gi, 0.13, 0.115, 0.39, -0.195);
        cloth(knee, trim, 0.116, 0.12, 0.045, -0.385);
        cloth(knee, skinMaterial, 0.063, 0.067, 0.11, -0.445);
        const foot = ellipsoid(knee, skinMaterial, [0, -0.49, 0.075], [0.082, 0.06, 0.17]);
        legs.push({ hip, knee, foot });
    }
    return { root, rig, torso, arms, legs, jacket };
}

// White projects blue, matching the supplied illustration.
const tori = makeJudoka(whiteMaterial, whiteTrim);
const uke = makeJudoka(blueMaterial, blueTrim);
scene.add(tori.root, uke.root);
const phases = [
    { name: 'Agarre y desequilibrio', detail: 'Blanco tira de manga y solapa', start: 0, end: 0.24 },
    { name: 'Entrada y giro', detail: 'Giro de espaldas y apoyo del pie izquierdo', start: 0.24, end: 0.49 },
    { name: 'Uchi Mata', detail: 'La pierna derecha eleva el muslo interior', start: 0.49, end: 0.75 },
    { name: 'Proyección y control', detail: 'Blanco acompaña la caída de azul', start: 0.75, end: 1.01 }
];
const duration = 16;
let elapsed = 0;
const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
let playing = !motionPreference.matches;
let previousTime = performance.now();
let cameraPreset = 0;
const cameraViews = [
    { label: 'Vista lateral', position: new THREE.Vector3(0.4, 2.1, 5.25) },
    { label: 'Vista opuesta', position: new THREE.Vector3(-3.2, 2.25, -4.5) },
    { label: 'Vista elevada', position: new THREE.Vector3(2.8, 4.1, 3.8) }
];
const timeline = document.querySelector('.technique-timeline');
const phaseButtons = [...document.querySelectorAll('.phase-button')];
const bounds = new THREE.Box3();
const toriTorsoBounds = new THREE.Box3();
const ukeTorsoBounds = new THREE.Box3();

function smooth(start, end, value) {
    const amount = THREE.MathUtils.clamp((value - start) / (end - start), 0, 1);
    return amount * amount * (3 - 2 * amount);
}

function resetPose(judoka) {
    judoka.root.position.set(0, 0, 0);
    judoka.root.rotation.set(0, 0, 0);
    judoka.rig.rotation.set(0, 0, 0);
    judoka.torso.rotation.set(0, 0, 0);
    for (const { hip, knee } of judoka.legs) {
        hip.rotation.set(0, 0, 0);
        knee.rotation.set(0, 0, 0);
    }
    for (const { shoulder, elbow } of judoka.arms) {
        shoulder.rotation.set(0, 0, 0);
        elbow.rotation.set(0, 0, 0);
    }
}

// Use the actual transformed vertices, not a fixed pelvis height.
function placeOnTatami(judoka, clearance = 0) {
    judoka.root.updateMatrixWorld(true);
    bounds.setFromObject(judoka.root, true);
    judoka.root.position.y += groundHeight + clearance - bounds.min.y;
    judoka.root.updateMatrixWorld(true);
}

function keepUkeOutsideTori() {
    const minimumBodyGap = 0.72;
    const outsideX = tori.root.position.x - minimumBodyGap;
    if (uke.root.position.x > outsideX) uke.root.position.x = outsideX;
    uke.root.updateMatrixWorld(true);

    toriTorsoBounds.setFromObject(tori.jacket);
    ukeTorsoBounds.setFromObject(uke.jacket);
    const overlap = new THREE.Vector3(
        Math.min(toriTorsoBounds.max.x, ukeTorsoBounds.max.x) - Math.max(toriTorsoBounds.min.x, ukeTorsoBounds.min.x),
        Math.min(toriTorsoBounds.max.y, ukeTorsoBounds.max.y) - Math.max(toriTorsoBounds.min.y, ukeTorsoBounds.min.y),
        Math.min(toriTorsoBounds.max.z, ukeTorsoBounds.max.z) - Math.max(toriTorsoBounds.min.z, ukeTorsoBounds.min.z)
    );
    if (overlap.x > 0.02 && overlap.y > 0.02 && overlap.z > 0.02) {
        const direction = Math.sign(ukeTorsoBounds.getCenter(new THREE.Vector3()).x - toriTorsoBounds.getCenter(new THREE.Vector3()).x) || -1;
        uke.root.position.x += direction * (overlap.x + 0.035);
        uke.root.updateMatrixWorld(true);
    }
}

function worldPoint(object, x, y, z) {
    return object.localToWorld(new THREE.Vector3(x, y, z));
}

// Two-bone IK keeps the hands at their grips throughout the turn.
function reach(judoka, armIndex, target) {
    const { shoulder, elbow } = judoka.arms[armIndex];
    const localTarget = judoka.torso.worldToLocal(target.clone());
    const direction = localTarget.sub(shoulder.position);
    const distance = THREE.MathUtils.clamp(direction.length(), 0.06, 0.819);
    direction.normalize();
    const bend = new THREE.Vector3(armIndex === 0 ? -1 : 1, -0.4, 0);
    bend.addScaledVector(direction, -bend.dot(direction)).normalize();
    const along = (0.42 ** 2 - 0.4 ** 2 + distance ** 2) / (2 * distance);
    const elbowOffset = direction.clone().multiplyScalar(along)
        .addScaledVector(bend, Math.sqrt(Math.max(0, 0.42 ** 2 - along ** 2)));
    shoulder.quaternion.setFromUnitVectors(down, elbowOffset.clone().normalize());
    const forearmDirection = direction.multiplyScalar(distance).sub(elbowOffset).normalize();
    forearmDirection.applyQuaternion(shoulder.quaternion.clone().invert());
    elbow.quaternion.setFromUnitVectors(down, forearmDirection);
    judoka.root.updateMatrixWorld(true);
}

function updateTechnique(time) {
    const kuzushi = smooth(0.04, 0.24, time);
    const entry = smooth(0.27, 0.44, time);
    const lift = smooth(0.52, 0.69, time);
    const fall = smooth(0.78, 0.94, time);
    resetPose(tori);
    resetPose(uke);
    tori.root.position.set(0.56 - 0.4 * entry, 0, 0.12 * entry);
    tori.root.rotation.y = -Math.PI / 2 + Math.PI * entry;
    tori.rig.rotation.x = 0.12 + 0.65 * lift - 0.2 * fall;
    tori.legs[0].hip.rotation.x = -0.16 - 0.55 * lift + 0.16 * fall;
    tori.legs[0].knee.rotation.x = 0.18;
    tori.legs[1].hip.rotation.x = -0.08 + 1.95 * lift + 0.9 * fall;
    tori.legs[1].hip.rotation.z = -0.13 * lift;
    tori.legs[1].knee.rotation.x = 0.12;
    placeOnTatami(tori);
    // The support foot stays at the pivot instead of sliding during the reap.
    const support = tori.legs[0].foot.getWorldPosition(new THREE.Vector3());
    tori.root.position.x += (0.48 - 0.42 * entry) - support.x;
    tori.root.position.z += -0.12 - support.z;
    tori.root.updateMatrixWorld(true);

    uke.root.position.set(-0.68 + 0.18 * kuzushi, 0, -0.13 - 0.24 * entry);
    uke.root.rotation.y = Math.PI / 2;
    uke.rig.rotation.x = 0.16 + 0.28 * kuzushi + 1.26 * lift + 1.85 * fall;
    uke.rig.rotation.z = -0.12 * lift - 0.22 * fall;
    uke.legs[0].hip.rotation.x = THREE.MathUtils.lerp(-0.14 - 0.58 * lift, -0.55, fall);
    uke.legs[0].hip.rotation.z = -0.15 - 0.2 * lift;
    uke.legs[0].knee.rotation.x = 0.2 + 0.1 * fall;
    uke.legs[1].hip.rotation.x = THREE.MathUtils.lerp(0.18 + 0.4 * lift, -0.4, fall);
    uke.legs[1].hip.rotation.z = 0.14 + 0.2 * lift;
    uke.legs[1].knee.rotation.x = 0.15 + 0.15 * fall;
    placeOnTatami(uke, 0.45 * lift * (1 - fall));

    for (let pass = 0; pass < 2; pass++) {
        keepUkeOutsideTori();
        reach(tori, 0, worldPoint(uke.torso, 0.3, 0.43, 0.08));
        reach(tori, 1, worldPoint(uke.torso, -0.12, 0.5, 0.2));
        reach(uke, 0, worldPoint(tori.torso, 0.12, 0.5, 0.2));
        const breakfall = worldPoint(uke.torso, -0.7, 0.25, 0.04);
        reach(uke, 1, worldPoint(tori.torso, -0.3, 0.43, 0.08).lerp(breakfall, fall));
        // Include hands and sleeves in floor clearance after solving grips.
        placeOnTatami(tori);
        keepUkeOutsideTori();
        placeOnTatami(uke, 0.45 * lift * (1 - fall));
    }
    const phaseIndex = Math.max(0, phases.findIndex(phase => time >= phase.start && time < phase.end));
    const phase = phases[phaseIndex];
    if (phaseLabel.textContent !== phase.name || detailLabel.textContent !== phase.detail) {
        phaseLabel.textContent = phase.name;
        detailLabel.textContent = phase.detail;
        stepLabel.textContent = `${String(phaseIndex + 1).padStart(2, '0')} / 04`;
    }
    timeline.value = String(time * 100);
    timeline.setAttribute('aria-valuetext', `${phase.name}, ${Math.round(time * 100)} %`);
    phaseButtons.forEach((button, index) => button.setAttribute('aria-pressed', String(index === phaseIndex)));
}

function syncPlayback() {
    const label = playing ? 'Pausar animación' : 'Reanudar animación';
    playbackButton.setAttribute('aria-label', label);
    playbackButton.title = label;
    playbackButton.firstElementChild.className = playing ? 'pause-icon' : 'play-icon';
}

function resize() {
    const { width, height } = canvas.getBoundingClientRect();
    renderer.setSize(width, height, false);
    camera.aspect = width / height;
    camera.fov = width < 440 ? 40 : 32;
    camera.updateProjectionMatrix();
}
new ResizeObserver(resize).observe(stage);
resize();
syncPlayback();
playbackButton.addEventListener('click', () => {
    if (elapsed >= duration) elapsed = 0;
    playing = !playing;
    syncPlayback();
    updateTechnique(elapsed / duration);
    renderer.render(scene, camera);
});
replayButton.addEventListener('click', () => {
    elapsed = 0;
    playing = true;
    syncPlayback();
    updateTechnique(elapsed / duration);
    renderer.render(scene, camera);
});
timeline.addEventListener('input', () => {
    elapsed = Number(timeline.value) / 100 * duration;
    playing = false;
    syncPlayback();
    updateTechnique(elapsed / duration);
    renderer.render(scene, camera);
});
phaseButtons.forEach((button, index) => button.addEventListener('click', () => {
    elapsed = [0.12, 0.46, 0.71, 0.97][index] * duration;
    playing = false;
    syncPlayback();
    updateTechnique(elapsed / duration);
    renderer.render(scene, camera);
}));
cameraButton.addEventListener('click', () => {
    cameraPreset = (cameraPreset + 1) % cameraViews.length;
    const view = cameraViews[cameraPreset];
    cameraLabel.textContent = view.label;
    camera.position.copy(view.position);
    controls.target.copy(viewTarget);
    controls.update();
});
motionPreference.addEventListener('change', event => {
    if (event.matches) {
        playing = false;
        syncPlayback();
    }
});
function animate(now) {
    requestAnimationFrame(animate);
    const delta = Math.min((now - previousTime) / 1000, 0.05);
    previousTime = now;
    if (playing) {
        elapsed = Math.min(elapsed + delta, duration);
        if (elapsed === duration) {
            playing = false;
            syncPlayback();
        }
    }
    updateTechnique(elapsed / duration);
    controls.update();
    renderer.render(scene, camera);
}
requestAnimationFrame(animate);